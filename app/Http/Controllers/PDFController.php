<?php


namespace App\Http\Controllers;


use App\Models\DetallesOrden;
use App\Models\OrdenesTrabajo;
use App\Models\ProductoStock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Mpdf\Mpdf;
use Mpdf\QrCode\QrCode;
use Mpdf\QrCode\Output;
use PDF;
use Carbon;
//use QrCode;


class PDFController extends Controller
{
    protected $mpdf;

    public function getOrden($id)
    {
        $orden = OrdenesTrabajo::find($id);
        $detalle = DetallesOrden::where('ordenTrabajo', $id)->get();
        $productos = ProductoStock::getProducts(Auth::user()['sucursal']);
        $detalle = $this->normalizeDetalle($detalle,$productos);

//        $qr = QrCode::format('svg')->size(100)->generate( 'Orden '.$orden->comprobante);
        $qrCode = new QrCode('Orden '.$orden->comprobante);
        $output = new Output\Png();
        $qr = $output->output($qrCode, 100, [255, 255, 255], [0, 0, 0], 9);
        $mytime = Carbon\Carbon::now();

        $data = [
            'orden' => $orden,
            'detalle' => $detalle,
            'fechaAhora' => $mytime->format("d/m/Y H:i"),
            'QR' =>""
        ];

        $this->mpdf = new Mpdf();
        $view = View::make('pdfOrden', $data);
        $html = $view->render();
        $this->mpdf->WriteHTML($html);
        $this->mpdf->page = 0;
        $this->mpdf->state = 0;
        unset($this->mpdf->pages[0]);
//
//        return $this->mpdf->Output($orden->comprobante . '.pdf', 'I');
        $mpdf = PDF::loadView('pdfOrden', $data,[],[
            'title' => 'Orden '.$orden->comprobante,
            'margin_top' => 5,
            'margin_bottom' => 5,
            'margin_left' => 5,
            'margin_right' => 5,
            'format'=>array(100, $this->mpdf->y+5)
        ]);
        return $mpdf->stream($orden->comprobante . '.pdf');
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
