@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Task List" />
    <div class="space-y-6">
        <x-common.component-card title="Basic Table 2">
            <x-tables.basic-tables.basic-tables-task />
        </x-common.component-card>
    </div>
@endsection
