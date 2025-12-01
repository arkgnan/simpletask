@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Create New Task" />
<div class="grid grid-cols-12 gap-4 md:gap-6">
    <div class="col-span-12 space-y-6 xl:col-span-12">
        <div class="rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex flex-col gap-2 px-5 mb-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <form action="{{ route('task.store') }}" method="POST" class="w-full">
                    @csrf

                    <div class="mb-4 w-full">
                        <x-forms.input
                            name="title"
                            label="Title"
                            type="text"
                            value="{{ old('title') }}"
                            :error="$errors->first('title')"
                        />
                    </div>

                    <div class="mb-4 w-full">
                        <x-forms.input
                            name="due_date"
                            label="Due Date"
                            type="date"
                            :value="old('due_date')"
                            :error="$errors->first('due_date')"
                        />
                    </div>

                    <div class="mb-4 w-full">
                        <x-forms.select
                            name="assignee_id"
                            label="Assigned To"
                            :options="$users->pluck('name', 'id')->toArray()"
                            :selected="old('assignee_id')"
                            :error="$errors->first('assignee_id')"
                        />
                    </div>

                    <div class="mb-4 w-full">
                        <x-forms.select
                            name="status"
                            label="Status"
                            :options="collect($statuses)->mapWithKeys(fn($enum) => [$enum->value => ucfirst(str_replace('_', ' ', $enum->value))])->toArray()"
                            :selected="old('status', App\Enums\TaskStatus::default()->value)"
                            :error="$errors->first('status')"
                        />
                    </div>

                    <div class="mb-4 w-full">
                        <x-forms.select
                            name="priority"
                            label="Priority"
                            :options="collect($priorities)->mapWithKeys(fn($enum) => [$enum->value => ucfirst($enum->value)])->toArray()"
                            :selected="old('priority', App\Enums\TaskPriority::default()->value)"
                            :error="$errors->first('priority')"
                        />
                    </div>

                    <div class="flex items-center justify-end mt-4 gap-3">
                        <a href="{{ route('task.index') }}">
                            <x-ui.button type="button" variant="outline" size="sm">
                                Cancel
                            </x-ui.button>
                        </a>

                        <x-ui.button type="submit" variant="primary" size="sm">
                            Create Task
                        </x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
