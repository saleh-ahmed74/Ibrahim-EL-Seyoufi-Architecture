<section class="admin-page">
    <div class="admin-page-heading"><div><span class="eyebrow">Messages</span><h1>Inquiries</h1></div><input type="search" wire:model.live.debounce.300ms="search" placeholder="Search messages"></div>
    <div class="admin-panel admin-table-wrap"><table class="admin-table"><thead><tr><th>Sender</th><th>Message</th><th>Received</th><th>Action</th></tr></thead><tbody>
        @forelse($inquiries as $inquiry)
            <tr><td><strong>{{ $inquiry->name ?: 'Visitor' }}</strong><small>{{ $inquiry->email }} @if($inquiry->phone) · {{ $inquiry->phone }} @endif</small></td><td class="inquiry-message">{{ $inquiry->message }}</td><td>{{ $inquiry->created_at?->format('Y-m-d H:i') }}</td><td><button type="button" class="danger-link" wire:click="delete({{ $inquiry->id }})" wire:confirm="Delete this inquiry?">Delete</button></td></tr>
        @empty
            <tr><td colspan="4" class="empty-state">No inquiries found.</td></tr>
        @endforelse
    </tbody></table></div>
    {{ $inquiries->links() }}
</section>
