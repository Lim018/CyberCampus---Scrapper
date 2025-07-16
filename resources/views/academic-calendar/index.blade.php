@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Data Kalender Akademik</h5>
                <a href="{{ route('academic-calendar.login') }}" class="btn btn-success">Update Data</a>
            </div>
            <div class="card-body">
                <!-- Filter Form -->
                <form method="GET" class="mb-4">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="class_filter" class="form-label">Filter Class</label>
                            <select name="class_filter" id="class_filter" class="form-select">
                                <option value="all" {{ request('class_filter') == 'all' ? 'selected' : '' }}>Semua</option>
                                <option value="odd" {{ request('class_filter') == 'odd' ? 'selected' : '' }}>Odd</option>
                                <option value="even" {{ request('class_filter') == 'even' ? 'selected' : '' }}>Even</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="order_by" class="form-label">Urutkan Berdasarkan</label>
                            <select name="order_by" id="order_by" class="form-select">
                                <option value="tanggal_mulai" {{ request('order_by') == 'tanggal_mulai' ? 'selected' : '' }}>Tanggal Mulai</option>
                                <option value="tanggal_selesai" {{ request('order_by') == 'tanggal_selesai' ? 'selected' : '' }}>Tanggal Selesai</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="order_direction" class="form-label">Arah Urutan</label>
                            <select name="order_direction" id="order_direction" class="form-select">
                                <option value="asc" {{ request('order_direction') == 'asc' ? 'selected' : '' }}>Ascending</option>
                                <option value="desc" {{ request('order_direction') == 'desc' ? 'selected' : '' }}>Descending</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary d-block">Filter</button>
                        </div>
                    </div>
                </form>

                <!-- Data Table -->
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kegiatan</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Selesai</th>
                                <th>Class Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kalender as $index => $item)
                                <tr class="{{ $item->class_type }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->kegiatan }}</td>
                                    <td>{{ $item->tanggal_mulai->format('d M Y') }}</td>
                                    <td>{{ $item->tanggal_selesai->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $item->class_type == 'odd' ? 'primary' : 'secondary' }}">
                                            {{ strtoupper($item->class_type) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">
                                        Tidak ada data. <a href="{{ route('academic-calendar.login') }}">Scrape data sekarang</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
