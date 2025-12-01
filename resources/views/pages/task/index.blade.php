@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Task List" />
<div class="grid grid-cols-12 gap-4 md:gap-6">
    <div class="col-span-12 space-y-6 xl:col-span-12">
        <div>
            @if(session()->has('success'))
                <div class="mb-4">
                    <x-ui.alert type="success" message="{{ session('success') }}" />
                </div>
            @endif
            <div class="rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">
                <!-- Header -->
                <div class="flex flex-col gap-2 px-5 mb-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <form action="{{ route('task.index') }}" method="GET">
                            <div class="relative">
                                <input type="text" name="title" placeholder="Search..." value="{{ request('title') }}" class="h-[42px] w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-[42px] pl-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800 xl:w-[300px]"/>

                                <button type="submit" class="absolute -translate-y-1/2 right-4 top-1/2">
                                    <svg class="fill-gray-500 dark:fill-gray-400" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M3.04199 9.37381C3.04199 5.87712 5.87735 3.04218 9.37533 3.04218C12.8733 3.04218 15.7087 5.87712 15.7087 9.37381C15.7087 12.8705 12.8733 15.7055 9.37533 15.7055C5.87735 15.7055 3.04199 12.8705 3.04199 9.37381ZM9.37533 1.54218C5.04926 1.54218 1.54199 5.04835 1.54199 9.37381C1.54199 13.6993 5.04926 17.2055 9.37533 17.2055C11.2676 17.2055 13.0032 16.5346 14.3572 15.4178L17.1773 18.2381C17.4702 18.531 17.945 18.5311 18.2379 18.2382C18.5308 17.9453 18.5309 17.4704 18.238 17.1775L15.4182 14.3575C16.5367 13.0035 17.2087 11.2671 17.2087 9.37381C17.2087 5.04835 13.7014 1.54218 9.37533 1.54218Z" fill=""/>
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>
                    <div>
                        <a href="{{ route('task.create') }}">
                            <x-ui.button type="button" variant="primary" size="sm">
                                Create New Task
                            </x-ui.button>
                        </a>
                    </div>
                </div>
                <div class="overflow-hidden">
                    <div class="max-w-full overflow-x-auto custom-scrollbar">
                        <table class="w-full min-w-[1102px]">
                            <thead>
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <th class="px-5 py-3 text-left sm:px-6">
                                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                            Title
                                        </p>
                                    </th>
                                    <th class="px-5 py-3 text-left sm:px-6">
                                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                            Due Date
                                        </p>
                                    </th>
                                    <th class="px-5 py-3 text-left sm:px-6">
                                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                            Assigned To
                                        </p>
                                    </th>
                                    <th class="px-5 py-3 text-left sm:px-6">
                                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                            Status
                                        </p>
                                    </th>
                                    <th class="px-5 py-3 text-left sm:px-6">
                                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                            Priority
                                        </p>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tasks as $task)
                                    <tr class="border-b border-gray-100 dark:border-white/[0.05]">
                                        <td class="px-4 sm:px-6 py-3.5">
                                            <span class="block font-medium text-gray-700 text-theme-sm dark:text-gray-400 hover:bg-sky-700">
                                                <a href="{{ route('task.show', $task->id) }}" class="hover:bg-sky-700">{{ $task->title }}</a>
                                            </span>
                                        </td>
                                        <td class="px-4 sm:px-6 py-3.5">
                                            <p class="text-gray-700 text-theme-sm dark:text-gray-400">{{ $task->due_date->format('Y-m-d') }}</p>
                                        </td>
                                        <td class="px-4 sm:px-6 py-3.5">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="flex items-center justify-center text-center overflow-hidden rounded-full h-10 w-10 bg-gray-200 text-black dark:bg-gray-700 dark:text-white">
                                                    {{ $task->assignee->initials() }}
                                                </div>
                                                <div>
                                                    <span class="mb-0.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">{{ $task->assignee->name }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 sm:px-6 py-3.5">
                                            @php
                                                $statusColors = [
                                                    'completed'   => 'bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-500',
                                                    'in_progress' => 'bg-blue-50 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400',
                                                    'pending'     => 'bg-gray-100 text-gray-700 dark:bg-gray-700/15 dark:text-gray-500',
                                                ];

                                                $colorClass = $statusColors[$task->status->value] ?? 'bg-gray-50 text-gray-700';
                                            @endphp
                                            <span class="text-theme-xs inline-block rounded-full px-2 py-0.5 font-medium {{ $colorClass }}">{{ ucfirst(str_replace('_', ' ', $task->status->value)) }}</span>
                                        </td>
                                        <td class="px-4 sm:px-6 py-3.5">
                                            @php
                                                $priorityColors = [
                                                    'high'   => 'bg-red-50 text-red-700 dark:bg-red-500/15 dark:text-red-500',
                                                    'medium' => 'bg-yellow-50 text-yellow-700 dark:bg-yellow-500/15 dark:text-yellow-400',
                                                    'low'     => 'bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-500',
                                                ];

                                                $colorPriorityClass = $priorityColors[$task->priority->value] ?? 'bg-gray-50 text-gray-700';
                                            @endphp
                                            <span class="text-theme-xs inline-block rounded-full px-2 py-0.5 font-medium {{ $colorPriorityClass }}">{{ ucfirst($task->priority->value) }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($tasks->total() > $tasks->perPage())
                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-white/[0.05]">
                    {{ $tasks->appends(request()->query())->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
