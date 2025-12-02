@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Detail Task" />
<div class="grid grid-cols-12 gap-4 md:gap-6">
    <div class="col-span-12 space-y-6 xl:col-span-12">
        @if(session()->has('success'))
            <div class="mb-4">
                <x-ui.alert type="success" message="{{ session('success') }}" />
            </div>
        @endif
        <div class="rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex flex-col gap-2 px-5 mb-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <form action="{{ route('task.update', $task->id) }}" method="POST" class="w-full">
                    @csrf
                    @method('PUT')
                    <div class="mb-4 w-full">
                        <x-forms.input
                            name="title"
                            label="Title"
                            type="text"
                            value="{{ $task->title }}"
                            :error="$errors->first('title')"
                        />
                    </div>

                    <div class="mb-4 w-full">
                        <x-forms.input
                            name="due_date"
                            label="Due Date"
                            type="date"
                            :value="$task->due_date ? $task->due_date->format('Y-m-d') : ''"
                            :error="$errors->first('due_date')"
                        />
                    </div>

                    <div class="mb-4 w-full">
                        <x-forms.select
                            name="assignee_id"
                            label="Assigned To"
                            :options="$users->pluck('name', 'id')->toArray()"
                            :selected="$task->assignee_id"
                            :error="$errors->first('assignee_id')"
                        />
                    </div>

                    <div class="mb-4 w-full">
                        <x-forms.textarea
                            name="description"
                            label="Description"
                            :value="$task->description"
                            :error="$errors->first('description')"
                        />
                    </div>

                    <div class="flex items-center justify-between mt-4 gap-3">
                        <div class="w-full">
                            <x-forms.select
                                name="status"
                                label="Status"
                                :options="collect($statuses)->mapWithKeys(fn($enum) => [$enum->value => ucfirst(str_replace('_', ' ', $enum->value))])->toArray()"
                                :selected="$task->status->value"
                                :error="$errors->first('status')"
                            />
                        </div>

                        <div class="w-full">
                            <x-forms.select
                                name="priority"
                                label="Priority"
                                :options="collect($priorities)->mapWithKeys(fn($enum) => [$enum->value => ucfirst($enum->value)])->toArray()"
                                :selected="$task->priority->value"
                                :error="$errors->first('priority')"
                            />
                        </div>
                    </div>


                    <div class="flex items-center justify-between mt-4 gap-3">
                        <div>
                            <a href="{{ route('task.index') }}">
                                <x-ui.button type="button" variant="outline" size="sm">
                                    Cancel
                                </x-ui.button>
                            </a>
                        </div>

                        <x-ui.button type="submit" variant="primary" size="sm">
                            Update Task
                        </x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
