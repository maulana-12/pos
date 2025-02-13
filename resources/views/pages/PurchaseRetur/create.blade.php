@extends('layouts.dashboard')

@section('title', 'Retur Pembelian')
@section('menu-title', 'Retur Pembelian')

@push('addon-style')
    <link href="{{ URL::asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('content')
    <div class="mt-5 border-0 card card-p-0 card-flush">
        <div class="mt-3">
            <form id="form1">
                <div class="row">
                    <div class="col-md-2">
                        <div class="mb-3 align-items-center">
                            <label for="inputEmail3" class="col-form-label">Tanggal</label>
                            <div class="input-group" id="kt_td_picker_date_only" data-td-target-input="nearest"
                                data-td-target-toggle="nearest">
                                <input id="kt_td_picker_date_only_input" type="text" class="form-control"
                                    data-td-target="#kt_td_picker_date_only" name="transaction_date"
                                    value="{{ date('Y-m-d') }}" disabled>
                                <!-- Set the value to today's date and make it readonly -->
                                <span class="input-group-text" data-td-target="#kt_td_picker_date_only"
                                    data-td-toggle="datetimepicker">
                                    <i class="ki-duotone ki-calendar fs-2"><span class="path1"></span><span
                                            class="path2"></span></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3 align-items-center">
                            <label for="invoice" class="col-form-label">Faktur</label>
                            <input id="invoice" type="text" name="invoice" class="form-control"
                                value="{{ $pembelian->invoice }}" readonly />
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="gap-2 py-5 card-header align-items-center gap-md-5">
            <div class="card-title">
                <!--begin::Search-->
                <div class="my-1 d-flex align-items-center position-relative">
                    <i class="ki-duotone ki-magnifier fs-1 position-absolute ms-4"><span class="path1"></span><span
                            class="path2"></span></i> <input type="text" data-kt-filter="search"
                        class="form-control form-control-solid w-250px ps-14" placeholder="Cari data barang">
                </div>
                <!--end::Search-->
            </div>
        </div>
        <div class="card-body">
            <div id="kt_datatable_example_wrapper dt-bootstrap4 no-footer" class="datatables_wrapper">
                <div class="table-responsive">
                    <table class="table align-middle border rounded table-row-dashed fs-6 g-5 dataTable no-footer"
                        id="kt_datatable_example">
                        <thead>
                            <tr class="text-start fw-bold fs-7 text-uppercase">
                                <th>Nama Barang</th>
                                <th>Unit</th>
                                <th>Harga</th>
                                <th>Quantity Pembelian</th>
                                <th>Quantity Retur</th>
                                <th>Total</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="fw-semibold">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row" style="margin-top: 100px">
            <div class="col">
                <div id="kt_datatable_example_wrapper dt-bootstrap4 no-footer" class="datatables_wrapper">
                    <div class="table-responsive">
                        <table class="table align-middle border rounded table-row-dashed fs-6 g-5 dataTable no-footer"
                            id="kt_datatable_cart">
                            <thead>
                                <tr class="text-start fw-bold fs-7 text-uppercase">
                                    <th>No</th>
                                    <th class="min-w-100px">Nama Barang</th>
                                    <th>Unit</th>
                                    <th>Jml Retur</th>
                                    <th>Harga</th>
                                    <th>Subtotal</th>
                                    <th>Hapus</th>
                                </tr>
                            </thead>
                            <tbody class="fw-semibold">
                                @forelse ($cart as $cart)
                                    <tr class="odd">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $cart->product->name }}</td>
                                        <td>{{ $cart->unit->name }}</td>
                                        <td>{{ $cart->quantity }}</td>
                                        <td>Rp{{ number_format($cart->price) }}</td>
                                        <td>Rp{{ number_format($cart->price * $cart->quantity) }}</td>
                                        <td>
                                            <form action="{{ route('pembelian-retur.destroyCart', $cart->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('delete')
                                                <button class="btn btn-sm btn-danger">
                                                    Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    {{-- show empty --}}
                                    <tr></tr>
                                    <td colspan="7" class="text-center">
                                        <h5 class="my-5">Tidak ada barang retur</h5>
                                    </td>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <form id="form2" action="{{ route('pembelian-retur.store') }}" method="post">
                    @csrf
                    <input type="hidden" name="purchase_id" id="purchase_id_form2" value="{{ $purchaseId }}">
                    <div class="mt-5 row">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('addon-script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ URL::asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>

    {{-- Datatables --}}
    <script>
        "use strict";

        // Class definition
        var KTDatatablesExample = function() {
            // Shared variables
            var table;
            var datatable;

            // Private functions
            var initDatatable = function() {
                // Set date data order
                const tableRows = table.querySelectorAll('tbody tr');
                // get id from route
                const id = window.location.pathname.split('/').pop();
                // Init datatable --- more info on datatables: https://datatables.net/manual/
                datatable = $(table).DataTable({
                    "info": false,
                    'order': [],
                    'pageLength': 10,
                    "ajax": {
                        url: '/pembelian/' + id,
                        type: 'GET',
                        dataSrc: '',
                    },
                    "columns": [{
                            data: 'product.name'
                        },
                        {
                            data: 'unit.name'
                        },
                        {
                            data: 'price_unit',
                            render: function(data, type, row) {
                                var formattedPrice = new Intl.NumberFormat('id-ID', {
                                    style: 'currency',
                                    currency: 'IDR'
                                }).format(data);
                                formattedPrice = formattedPrice.replace(",00", "");
                                return formattedPrice;
                            }
                        },
                        {
                            data: 'quantity'
                        },
                        {
                            data: null,
                            render: function(data, type, row) {
                                return `
                                <input type="number" name="quantity_retur" class="form-control">
                                <input type="hidden" name="unit_retur" value="${row.unit.id}">
                                <input type="hidden" name="price_retur" value="${row.total_price}">
                                `;

                            }
                        },
                        {
                            data: 'total_price',
                            render: function(data, type, row) {
                                var total = row.quantity * data;
                                var formattedPrice = new Intl.NumberFormat('id-ID', {
                                    style: 'currency',
                                    currency: 'IDR'
                                }).format(total);
                                formattedPrice = formattedPrice.replace(",00", "");
                                return formattedPrice;
                            }
                        },
                        {
                            data: null,
                            className: 'text-center',
                            render: function(data, type, row) {
                                return `<button class="btn btn-warning btn-submit" data-product-id="${row.product.id}">Pilih</button>`;
                            }
                        }

                    ],
                });

                $(table).on('click', '.btn-submit', function() {
                    var rowData = datatable.row($(this).closest('tr')).data();
                    var productId = $(this).data('product-id');
                    var quantityRetur = $(this).closest('tr').find('input[name="quantity_retur"]').val();
                    var unitRetur = $(this).closest('tr').find('input[name="unit_retur"]').val();
                    var priceRetur = $(this).closest('tr').find('input[name="price_retur"]').val();

                    var inputRequest = {
                        product_id: productId,
                        quantity: quantityRetur,
                        unit_id: unitRetur,
                        price: priceRetur
                    };

                    console.log(inputRequest);

                    // Send AJAX request
                    $.ajax({
                        url: '{{ route('pembelian-retur.addCart') }}',
                        type: 'POST',
                        data: inputRequest,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            // reload page
                            location.reload();
                        },
                        error: function(xhr, status, error) {
                            var err = eval("(" + xhr.responseText + ")");
                            console.log(err);
                        }
                    });
                });
            }

            // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
            var handleSearchDatatable = () => {
                const filterSearch = document.querySelector('[data-kt-filter="search"]');
                filterSearch.addEventListener('keyup', function(e) {
                    datatable.search(e.target.value).draw();
                });
            }

            // Public methods
            return {
                init: function() {
                    table = document.querySelector('#kt_datatable_example');

                    if (!table) {
                        return;
                    }
                    $(table).on('keydown', 'input[name^="quantity_"]', function(event) {
                        if(event.which === 13) {
                            event.preventDefault();
                            var btnSubmit = $(this).closest('tr').find('.btn-submit');
                            btnSubmit.click();
                        }
                    });
                    initDatatable();
                    handleSearchDatatable();
                }
            };
        }();

        // On document ready
        KTUtil.onDOMContentLoaded(function() {
            KTDatatablesExample.init();
        });
    </script>
@endpush
