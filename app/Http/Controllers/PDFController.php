<?php


namespace App\Http\Controllers;


use App\Models\DetallesOrden;
use App\Models\OrdenesTrabajo;
use App\Models\ProductoStock;
use App\Models\Recibo;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
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

    public function printHtml(Request $request)
    {
        try {
            request()->validate([
                'body' => 'required',
            ]);

            $this->mpdf = new Mpdf(['tempDir' => storage_path('app/tmp')]);
            $view = View::make('pdfClean', $request->all());
            $html = $view->render();
            $this->mpdf->WriteHTML($html);
            $this->mpdf->page = 0;
            $this->mpdf->state = 0;
            unset($this->mpdf->pages[0]);
            $mpdfView = PDF::loadView('pdfClean', $request->all(), [], [
                'title' => $request->title,
                'margin_top' => 5,
                'margin_bottom' => 5,
                'margin_left' => 5,
                'margin_right' => 5,
                'format' => array(72.1, $this->mpdf->y + 15),
                'orientation' => 'P'
            ]);
            return $mpdfView->stream($request->title . '.pdf');
        } catch (Exception $error) {
            Log::error($error->getMessage());
            abort(404);
        }
    }
}

