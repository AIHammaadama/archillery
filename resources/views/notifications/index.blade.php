@extends('layouts.admin')

@section('title', 'Notifications | PPMS')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <!-- Page header -->
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Notifications</h4>
                    <p class="mb-0">View and manage your notifications</p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Notifications</li>
                </ol>
            </div>
        </div>

        <!-- Notification list -->
        <div class="row" x-data="notifications()">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">All Notifications</h4>
                        <button type="button" class="btn btn-sm btn-primary" @click="markAllAsRead()"
                            x-show="unreadCount > 0">
                            <i class="bi bi-check-all"></i> Mark all as read
                        </button>
                    </div>
                    <div class="card-body">
                        @if($notifications->count() > 0)
                        <div class="notification-list">
                            @foreach($notifications as $notification)
                            <div
                                class="notification-item p-3 border-bottom {{ is_null($notification->read_at) ? 'bg-light' : '' }}">
                                <div class="d-flex align-items-start">
                                    <div class="me-3">
                                        <div class="notification-icon">
                                            @if(str_contains($notification->type, 'Request'))
                                            <i class="bi bi-file-earmark-text text-primary fs-3"></i>
                                            @elseif(str_contains($notification->type, 'Project'))
                                            <i class="bi bi-folder text-success fs-3"></i>
                                            @elseif(str_contains($notification->type, 'Delivery'))
                                            <i class="bi bi-truck text-info fs-3"></i>
                                            @elseif(str_contains($notification->type, 'Approval'))
                                            <i class="bi bi-check-circle text-warning fs-3"></i>
                                            @else
                                            <i class="bi bi-bell text-secondary fs-3"></i>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">
                                            {{ $notification->data['title'] ?? 'Notification' }}
                                            @if(is_null($notification->read_at))
                                            <span class="badge badge-danger badge-sm ms-2">New</span>
                                            @endif
                                        </h6>
                                        <p class="mb-1 text-muted">
                                            {{ $notification->data['message'] ?? 'No message' }}
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <small class="text-muted">
                                                <i class="bi bi-clock me-1"></i>
                                                {{ $notification->created_at->diffForHumans() }}
                                            </small>
                                            <div>
                                                @if(isset($notification->data['url']))
                                                <a href="{{ $notification->data['url'] }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    View Details
                                                </a>
                                                @endif
                                                @if(is_null($notification->read_at))
                                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                                    @click="markAsRead('{{ $notification->id }}')">
                                                    Mark as read
                                                </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <!-- Pagination -->
                        @if($notifications->hasPages())
                        <div class="mt-4">
                            {{ $notifications->links('pagination::bootstrap-4') }}
                        </div>
                        @endif
                        @else
                        <div class="text-center py-5">
                            <i class="bi bi-bell-slash display-1 text-muted"></i>
                            <h5 class="mt-3">No notifications yet</h5>
                            <p class="text-muted">You don't have any notifications at the moment.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection