@extends('layouts.admin')

@section('header', 'Admin Dashboard')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1">Total Orders</p>
                    <h3 class="fw-bold" style="color: #192C57;">1,245</h3>
                </div>
                <div class="icon-box bg-light rounded-circle p-3">
                    <i class="fas fa-shopping-basket fa-2x" style="color: #CEAA0C;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1">Total Revenue</p>
                    <h3 class="fw-bold" style="color: #192C57;">KES 45,000</h3>
                </div>
                <div class="icon-box bg-light rounded-circle p-3">
                    <i class="fas fa-coins fa-2x" style="color: #CEAA0C;"></i>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection