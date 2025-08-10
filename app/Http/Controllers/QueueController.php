<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use App\Models\Service;
use Illuminate\Http\Request;

class QueueController extends Controller
{
    public function startQueue(Request $request)
    {
        $serviceId = $request->input('service_id');
        $adminId = $request->input('admin_id');

        $firstQueue = Queue::where('service_id', $serviceId)
                            ->where('status', 'waiting')
                            ->orderBy('created_at')
                            ->orderBy('id')
                            ->first();

        if ($firstQueue) {
            $firstQueue->status = 'called';
            $firstQueue->called_at = now();
            $firstQueue->admin_id = $adminId;
            $firstQueue->save();

            return response()->json(['message' => 'Antrian dimulai', 'queue' => $firstQueue]);
        }

        return response()->json(['message' => 'Tidak ada antrian untuk dimulai'], 404);
    }

    public function takeQueue(Request $request)
    {
        $serviceId = $request->input('service_id');

        if (!$serviceId) {
            return response()->json(['message' => 'service_id wajib diisi'], 400);
        }

        $service = Service::find($serviceId);
        if (!$service) {
            return response()->json(['message' => 'Layanan tidak ditemukan'], 404);
        }

        $today = now()->toDateString();
        $queueCountToday = Queue::where('service_id', $serviceId)
                                ->whereDate('queue_date', $today)
                                ->count();

        $queueNumber = $service->prefix . str_pad($queueCountToday + 1, 3, '0', STR_PAD_LEFT);

        $queue = Queue::create([
            'service_id' => $serviceId,
            'queue_number' => $queueNumber,
            'status' => 'waiting',
            'queue_date' => $today,
            'created_at' => now(),
        ]);

        return response()->json([
            'message' => 'Berhasil mengambil antrian',
            'queue' => $queue
        ]);
    }

    public function queueList(Request $request)
    {
        $serviceId = $request->query('service_id');

        $query = Queue::with(['service', 'admin'])->orderBy('queue_date');

        if ($serviceId) {
            $query->where('service_id', $serviceId);
        }

        $queues = $query->get();

        return response()->json($queues);
    }

    public function next(Request $request)
    {
        $serviceId = $request->input('service_id');
        $adminId = $request->input('admin_id');

        $currentQueue = Queue::where('service_id', $serviceId)
                            ->where('admin_id', $adminId)
                            ->where('status', 'called')
                            ->first();

        if ($currentQueue) {
            $currentQueue->status = 'completed';
            $currentQueue->completed_at = now();
            $currentQueue->save();
        }

        $nextQueue = Queue::where('service_id', $serviceId)
                        ->where('status', 'waiting')
                        ->orderBy('created_at')
                        ->orderBy('id')
                        ->first();

        if ($nextQueue) {
            $nextQueue->status = 'called';
            $nextQueue->called_at = now();
            $nextQueue->admin_id = $adminId;
            $nextQueue->save();

            return response()->json($nextQueue);
        }

        return response()->json(['message' => 'Tidak ada antrian berikutnya'], 404);
    }

    public function prev(Request $request)
    {
        $serviceId = $request->input('service_id');
        $adminId = $request->input('admin_id');

        $currentQueue = Queue::where('service_id', $serviceId)
                            ->where('admin_id', $adminId)
                            ->where('status', 'called')
                            ->first();

        if ($currentQueue) {
            $currentQueue->status = 'waiting';
            $currentQueue->save();
        }

        $prevCompletedQueue = Queue::where('service_id', $serviceId)
                                ->where('admin_id', $adminId)
                                ->where('status', 'completed')
                                ->orderByDesc('completed_at')
                                ->first();

        if ($prevCompletedQueue) {
            $prevCompletedQueue->status = 'called';
            $prevCompletedQueue->called_at = now();
            $prevCompletedQueue->save();

            return response()->json($prevCompletedQueue);
        }

        return response()->json(['message' => 'Tidak ada antrian sebelumnya'], 404);
    }
}
