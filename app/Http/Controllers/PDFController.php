<?php


namespace App\Http\Controllers;


use App\Models\DetallesOrden;
use App\Models\OrdenesTrabajo;
use App\Models\ProductoStock;
use App\Models\Recibo;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Mpdf\Mpdf;
use PDF;
use Ramsey\Collection\Collection;
use UnexpectedValueException;


class PDFController extends Controller
{
    protected $mpdf;

    public function getOrdenVenta($id)
    {
        $this->getOrden($id, true);
    }

    public function getOrdenDiseño($id)
    {
        $this->getOrden($id, false);
    }

    private function getOrden($id, bool $isVenta)
    {
        try {
            $orden = OrdenesTrabajo::find($id);
            if (empty($orden)) {
                throw new UnexpectedValueException("pdf: no data for {$id}");
            }
            $detalle = DetallesOrden::where('ordenTrabajo', $id)->get();
            $productos = ProductoStock::getProducts(Auth::user()['sucursal']);
            $detalle = $this->normalizeDetalle($detalle, $productos);

            $mytime = Carbon::parse($orden->updated_at);

            $data = [
                'orden' => $orden,
                'detalle' => $detalle,
                'fechaOrden' => $mytime->format("d/m/Y H:i"),
                'isVenta' => $isVenta
            ];

            $this->mpdf = new Mpdf();
            $view = View::make('pdfOrden', $data);
            $html = $view->render();
            $this->mpdf->WriteHTML($html);
            $this->mpdf->page = 0;
            $this->mpdf->state = 0;
            unset($this->mpdf->pages[0]);
            $mpdfView = PDF::loadView('pdfOrden', $data, [], [
                'title' => 'Orden ' . $orden->codigoServicio,
                'margin_top' => 5,
                'margin_bottom' => 5,
                'margin_left' => 5,
                'margin_right' => 5,
                'format' => array(72.1, $this->mpdf->y + 15),
                'orientation' => 'P'
            ]);
            return $mpdfView->stream($orden->codigoServicio . '.pdf');
        } catch (Exception $error) {
            Log::error($error->getMessage());
            abort(404);
        }
    }

    function getProduct($id, Collection $products)
    {
        $item = $products->first(function ($value, $key) use ($id) {
            return $value->id == $id;
        });
        if ($item) {
            return "{$item->formato} ({$item->dimension})";
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

    public function getRecibo($id)
    {
        try {
            $recibo = Recibo::find($id);
            if (empty($recibo)) {
                throw new UnexpectedValueException("pdf: no data for {$id}");
            }
            $mytime = Carbon::parse($recibo->updated_at);

            $data = [
                'recibo' => $recibo,
                'fechaOrden' => $mytime->format("d/m/Y H:i"),
            ];

            $this->mpdf = new Mpdf();
            $view = View::make('pdfRecibo', $data);
            $html = $view->render();
            $this->mpdf->WriteHTML($html);
            $this->mpdf->page = 0;
            $this->mpdf->state = 0;
            unset($this->mpdf->pages[0]);
            $mpdfView = PDF::loadView('pdfRecibo', $data, [], [
                'title' => 'Recibo ' . $recibo->secuencia,
                'margin_top' => 5,
                'margin_bottom' => 5,
                'margin_left' => 5,
                'margin_right' => 5,
                'format' => array(72.1, $this->mpdf->y + 15),
                'orientation' => 'P'
            ]);
            return $mpdfView->stream($recibo->secuencia . '.pdf');
        } catch (Exception $error) {
            Log::error($error->getMessage());
            abort(404);
        }
    }
}
