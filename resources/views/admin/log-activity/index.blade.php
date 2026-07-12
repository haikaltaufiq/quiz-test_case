<x-app-layout>
    <!-- Activity Logs Section (Bottom, Full Width) -->
    <div class=" pt-6">
        <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3">System Activity Logs</h2>
        <div class="bg-white border border-gray-200 rounded-md overflow-hidden">
            <table class="min-w-full divide-y divide-gray-100 text-xs">
                <thead class="bg-gray-50 text-gray-400 font-semibold uppercase">
                    <tr>
                        <th class="px-4 py-2 text-left">Time</th>
                        <th class="px-4 py-2 text-left">User</th>
                        <th class="px-4 py-2 text-left">Action</th>
                        <th class="px-4 py-2 text-left">Description</th>
                        <th class="px-4 py-2 text-left">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-600">
                    @forelse($activityLogs as $log)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-4 py-2.5 whitespace-nowrap text-gray-400">
                                {{ $log->created_at->format('M d, H:i:s') }}</td>
                            <td class="px-4 py-2.5 whitespace-nowrap font-medium text-gray-950">
                                {{ $log->user ? $log->user->name : 'System' }}</td>
                            <td class="px-4 py-2.5 whitespace-nowrap">
                                @php
                                    $badgeVar = 'gray';
                                    if (Str::contains($log->action, 'created')) {
                                        $badgeVar = 'blue';
                                    }
                                    if (Str::contains($log->action, 'published')) {
                                        $badgeVar = 'green';
                                    }
                                    if (Str::contains($log->action, 'submitted')) {
                                        $badgeVar = 'green';
                                    }
                                    if (Str::contains($log->action, 'deleted')) {
                                        $badgeVar = 'red';
                                    }
                                    if (Str::contains($log->action, 'graded')) {
                                        $badgeVar = 'yellow';
                                    }
                                @endphp
                                <x-ui.badge :variant="$badgeVar">{{ $log->action }}</x-ui.badge>
                            </td>
                            <td class="px-4 py-2.5">{{ $log->description }}</td>
                            <td class="px-4 py-2.5 whitespace-nowrap text-gray-400">{{ $log->ip_address }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-400 italic">No activities
                                logged yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
