<?php


namespace App\Http\Controllers;


use App\Models\DetallesOrden;
use App\Models\OrdenesTrabajo;
use App\Models\ProductoStock;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use PDF;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PDFController extends Controller
{
    protected $mpdf;

    public function getOrden($id)
    {
        try {
            $orden = OrdenesTrabajo::find($id);
            if (empty($orden)) {
                throw new \UnexpectedValueException("pdf: no data for {$id}");
            }
            $detalle = DetallesOrden::where('ordenTrabajo', $id)->get();
            $productos = ProductoStock::getProducts(Auth::user()['sucursal']);
            $detalle = $this->normalizeDetalle($detalle, $productos);
            $qr = QrCode::style('round')
                ->format('png')
                ->size(100)
                ->generate('Orden ' . $orden->comprobante);
            $qr = base64_encode($qr);
            $mytime = Carbon::now();

            $data = [
                'orden' => $orden,
                'detalle' => $detalle,
                'fechaAhora' => $mytime->format("d/m/Y H:i"),
                'QR' => $qr
            ];
            $html = View::make('pdfOrden', $data);

            $dompdf = new Dompdf();
            $_dompdf_paper_size=self::paperFormat();
            $dompdf->setPaper($_dompdf_paper_size);

            $GLOBALS['bodyHeight'] = 0;

            $dompdf->setCallbacks(
                array(
                    'myCallbacks' => array(
                        'event' => 'end_frame', 'f' => function ($infos) {
                            $frame = $infos["frame"];
                            if (strtolower($frame->get_node()->nodeName) === "body") {
                                $padding_box = $frame->get_padding_box();
                                $GLOBALS['bodyHeight'] += $padding_box['h'];
                            }
                        }
                    )
                )
            );

            $dompdf->loadHtml($html);
            $dompdf->render();
            unset($dompdf);

            if( ! empty( $_dompdf_paper_size  ) ) {
                $_dompdf_paper_size[3] = $GLOBALS['bodyHeight'] + 30;
            }
            $pdf = PDF::loadHTML($html)
                ->setPaper($_dompdf_paper_size);
            return $pdf->stream($orden->comprobante . '.pdf');
        } catch (\Exception $error) {
            Log::error($error->getMessage());
            abort(404);
        }
    }

    function getProduct($id, $products)
    {
        $item = [];
        foreach ($products as $product) {
            if ($product->id == $id) {
                $item = $product;
                break;
            }
        }
        if ($item) {
            return $item->formato . ' (' . $item->dimension . ')';
        }
        return "";
    }

    function normalizeDetalle($detalle, $productos)
    {
        foreach ($detalle as $key => $item) {
            $detalle[$key]->stock = $this->getProduct($item->stock, $productos);
        }
        return $detalle;
    }

    function paperFormat()
    {
        // change the values below
        $width = 80; //mm!
        $height = 115; //mm!

        //convert mm to points
        $paper_format = array(0, 0, ($width / 25.4) * 72, ($height / 25.4) * 72);

        return $paper_format;
    }
}
