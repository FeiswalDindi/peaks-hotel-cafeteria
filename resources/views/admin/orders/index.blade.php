@extends('layouts.admin')

@section('header', $activeTab == 'general' ? 'General Order Management' : 'Internal Staff Ledger')

@section('content')
<style>
    /* 🌟 Master Mobile Accordion Styles */
    @media (max-width: 768px) {
        .mobile-clickable-row { cursor: pointer; transition: background-color 0.2s; }
        .mobile-clickable-row:active { background-color: #f0f0f0; }
        .mobile-expand-icon { transition: transform 0.3s ease; }
        tr[aria-expanded="true"] .mobile-expand-icon { transform: rotate(180deg); color: #CEAA0C !important; }
        .detail-row td { padding: 0 !important; border: none; }
        .detail-content { 
            padding: 1.25rem; 
            border-left: 4px solid #192C57; 
            background-color: #f8f9fc; 
            box-shadow: inset 0 3px 6px rgba(0,0,0,0.02);
        }
    }
</style>

    @if($activeTab == 'general')
        @include('admin.orders.generalorders')
    @elseif($activeTab == 'ledger')
        @include('admin.orders.staffledger')
    @endif

@endsection