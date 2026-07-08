@extends('admin.layouts.app')

@section('title', __('messages.versions') . ' - ' . __('messages.settings'))
@section('page_title', __('messages.versions'))

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Add Version Form (Left Column) -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 space-y-4 self-start">
        <h3 class="font-extrabold text-lg text-gray-900 border-b border-gray-50 pb-2">{{ __('messages.publish_new_version') }}</h3>
        
        <form action="{{ route('admin.versions.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">{{ __('messages.version_string') }}</label>
                <input type="text" name="version" required placeholder="e.g. 1.2.0" class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">{{ __('messages.download_url') }}</label>
                <input type="url" name="download_url" required placeholder="https://example.com/downloads/app.exe" class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm">
            </div>

            <div class="flex items-center space-x-2">
                <input type="checkbox" id="force_update" name="force_update" value="1" class="rounded border-gray-300 text-emerald-500 focus:ring-emerald-500/20">
                <label for="force_update" class="text-sm font-semibold text-gray-600 cursor-pointer">{{ __('messages.force_update') }}</label>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">{{ __('messages.release_notes') }}</label>
                <textarea name="release_note" rows="4" placeholder="Describe the new features and bug fixes..." class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm"></textarea>
            </div>

            <button type="submit" class="w-full py-3 text-sm font-bold rounded-2xl bg-gradient-to-r from-cyan-500 to-emerald-500 text-white hover:from-cyan-600 hover:to-emerald-600 shadow-md shadow-cyan-200/50 transition-all">
                {{ __('messages.publish_btn') }}
            </button>
        </form>
    </div>

    <!-- Version List Table (Right Column - Occupies 2 cols) -->
    <div class="lg:col-span-2 bg-white rounded-3xl border border-gray-100 shadow-sm p-6 overflow-hidden">
        <h3 class="font-extrabold text-lg text-gray-900 mb-4 border-b border-gray-50 pb-2">{{ __('messages.version_history') }}</h3>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 text-xs text-gray-400 uppercase font-bold bg-gray-50/50">
                        <th class="p-3">{{ __('messages.versions') }}</th>
                        <th class="p-3">{{ __('messages.force_update') }}</th>
                        <th class="p-3">{{ __('messages.download_url') }}</th>
                        <th class="p-3">{{ __('messages.published_date') }}</th>
                        <th class="p-3 text-right">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm">
                    @forelse($versions as $version)
                        <tr class="hover:bg-gray-50/30 transition-colors">
                            <td class="p-3">
                                <span class="font-bold text-gray-900 block text-base">{{ $version->version }}</span>
                                @if($version->release_note)
                                    <span class="block text-xs text-gray-400 mt-1 max-w-xs truncate">{{ $version->release_note }}</span>
                                @endif
                            </td>
                            <td class="p-3">
                                @if($version->force_update)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700">Yes (Force)</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">Optional</span>
                                @endif
                            </td>
                            <td class="p-3">
                                <a href="{{ $version->download_url }}" target="_blank" class="text-xs text-emerald-600 hover:text-emerald-700 hover:underline max-w-xs block truncate font-mono">{{ $version->download_url }}</a>
                            </td>
                            <td class="p-3 text-gray-500 text-xs font-medium">
                                {{ $version->created_at->format('Y-m-d H:i') }}
                            </td>
                            <td class="p-3 text-right">
                                <form action="{{ route('admin.versions.destroy', $version->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this version?')">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 text-xs font-bold text-red-600 hover:text-white hover:bg-red-500 rounded-xl transition-colors">{{ __('messages.delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-400">{{ __('messages.no_data') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $versions->links() }}
        </div>
    </div>

</div>
@endsection
