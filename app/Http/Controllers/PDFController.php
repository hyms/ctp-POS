<?php


namespace App\Http\Controllers;


use App\Models\DetallesOrden;
use App\Models\OrdenesTrabajo;
use App\Models\ProductoStock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Mpdf\Mpdf;
//use Mpdf\QrCode\QrCode;
//use Mpdf\QrCode\Output;
use PDF;
use Carbon;
//use QrCode;


class PDFController extends Controller
{
    protected $mpdf;

    public function getOrden($id)
    {
        try {
            $orden = OrdenesTrabajo::find($id);
            if(empty($orden))
            {
                throw new \Exception("no data");
            }
            $detalle = DetallesOrden::where('ordenTrabajo', $id)->get();
            $productos = ProductoStock::getProducts(Auth::user()['sucursal']);
            $detalle = $this->normalizeDetalle($detalle, $productos);
            $qr = QrCode::style('round')
                ->format('png')
                ->size(150)
                ->generate('Orden ' . $orden->comprobante);
            $qr = base64_encode($qr);
            $mytime = Carbon\Carbon::now();

            $data = [
                'orden' => $orden,
                'detalle' => $detalle,
                'fechaAhora' => $mytime->format("d/m/Y H:i"),
                'QR' => $qr
            ];

            $this->mpdf = new Mpdf();
            $view = View::make('pdfOrden', $data);
            $html = $view->render();
            $this->mpdf->WriteHTML($html);
            $this->mpdf->page = 0;
            $this->mpdf->state = 0;
            unset($this->mpdf->pages[0]);
            $mpdf = PDF::loadView('pdfOrden', $data, [], [
                'title' => 'Orden ' . $orden->comprobante,
                'margin_top' => 5,
                'margin_bottom' => 5,
                'margin_left' => 5,
                'margin_right' => 5,
//            'format'=>array(80, $this->mpdf->y+5)
                'format' => array(80, $this->mpdf->y)
            ]);
            return $mpdf->stream($orden->comprobante . '.pdf');
        }
        catch (\Exception $error)
        {
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

    function normalizeDetalle($detalle,$productos)
    {
        foreach ($detalle as $key=>$item)
        {
            $detalle[$key]->stock = $this->getProduct($item->stock,$productos);
        }
        return $detalle;
    }
}
