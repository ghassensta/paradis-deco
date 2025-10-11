<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Avis;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
class AvisController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('admin.avis.index', compact('products'));
    }

    public function getAvis(Request $request)
    {
        $draw = $request->input('draw', 1);
        $start = $request->input('start', 0);
        $rowPerPage = $request->input('length', 10);
        $columnIndex = 0;
        $columnName = 'id';
        $columnSortOrder = 'asc';
        $searchValue = '';
        $product_id = $request->input('product_id');

        // Order information
        $order = $request->input('order');
        if (is_array($order) && !empty($order) && isset($order[0]['column']) && isset($order[0]['dir'])) {
            $columnIndex = $order[0]['column'];
            $columnSortOrder = $order[0]['dir'];
        }

        // Column name
        $columns = $request->input('columns');
        if (is_array($columns) && isset($columns[$columnIndex]['data'])) {
            $columnName = $columns[$columnIndex]['data'];
        }

        // Column mapping
        $columnMap = [
            'id' => 'id',
            'product_id' => 'product_id',
            'rating' => 'rating',
            'comment' => 'comment',
            'name' => 'name',
            'approved' => 'approved',
        ];

        $dbColumnName = $columnMap[$columnName] ?? 'id';

        // Search value
        $search = $request->input('search');
        if (is_array($search) && isset($search['value'])) {
            $searchValue = $search['value'];
        }

        // Build query
        $query = Avis::select([
            'avis.id',
            'avis.product_id',
            'avis.rating',
            'avis.comment',
            'avis.name',
            'avis.approved',
            'avis.created_at'
        ])
            ->with(['product:id,name'])
            ->orderByDesc('created_at');

        // Apply search filter
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('name', 'like', '%' . $searchValue . '%')
                  ->orWhere('comment', 'like', '%' . $searchValue . '%')
                  ->orWhereHas('product', function ($q) use ($searchValue) {
                      $q->where('name', 'like', '%' . $searchValue . '%');
                  });
            });
        }

        // Apply product filter
        if ($product_id && $product_id !== 'all') {
            $query->where('product_id', $product_id);
        }

        // Get total records
        $totalRecords = $query->count();

        // Apply sorting and pagination
        $avis = $query->orderBy($dbColumnName, $columnSortOrder)
            ->offset($start)
            ->limit($rowPerPage)
            ->get();

        // Map data for DataTables
        $data_arr = $avis->map(function ($row) {
            return [
                'id' => $row->id,
                'date' => $row->created_at?->format('d-m-Y') ?? 'N/A',
                'product' => optional($row->product)->name ?? 'N/A',
                'rating' => $row->rating ?? 0,
                'comment' => $row->comment ?? 'N/A',
                'name' => $row->name ?? 'N/A',
                'approved' => $row->approved ? 'Approuvé' : 'En attente'
            ];
        })->toArray();

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data_arr,
        ]);
    }

    public function create()
    {
        $products = Product::all();
        return view('admin.avis.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
            'name' => 'required|string|max:255',
            'approved' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $avis = Avis::create($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Avis créé avec succès'
        ]);
    }

    public function edit($id)
    {
        $avis = Avis::findOrFail($id);
        $products = Product::all();
        return view('admin.avis.edit', compact('avis', 'products'));
    }

    public function update(Request $request, $id)
    {
        $avis = Avis::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
            'name' => 'required|string|max:255',
            'approved' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $avis->update($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Avis mis à jour avec succès'
        ]);
    }

    public function destroy($id)
    {
        $avis = Avis::findOrFail($id);
        $avis->delete();
        return response()->json([
            'success' => true,
            'message' => 'Avis supprimé avec succès'
        ]);
    }
}
