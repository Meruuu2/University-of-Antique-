@extends('layouts.admin')

@section('admin-content')
@php
    $monthLabel = $currentMonth->format('F Y');
    $prevMonth = $currentMonth->copy()->subMonth()->format('Y-m');
    $nextMonth = $currentMonth->copy()->addMonth()->format('Y-m');
    $labels = array_keys($series);   // facility labels
    $data   = array_values($series); // counts
@endphp

<div class="fms-card">
    <div class="fms-page-header border-0 pb-0 mb-4">
        <div>
            <h1 class="fms-page-title">Monthly Overview</h1>
            <p class="text-xs text-neutral-600">
                Number of approved/rescheduled bookings this month per facility
                (custom college / org facilities are grouped under "OTHERS").
            </p>
        </div>
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.overview', ['month' => $prevMonth]) }}" class="fms-link">← Prev</a>
            <span class="text-neutral-600">{{ $monthLabel }}</span>
            <a href="{{ route('admin.overview', ['month' => $nextMonth]) }}" class="fms-link">Next →</a>
        </div>
    </div>

    <div class="border border-black p-4 overflow-x-auto">
        <canvas id="fmsOverviewChart" width="900" height="320"></canvas>
    </div>
</div>

<script>

    (function() {
        const labels = @json($labels);
        const data   = @json($data);

        const canvas = document.getElementById('fmsOverviewChart');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        const width = canvas.width;
        const height = canvas.height;

        const paddingLeft = 40;
        const paddingRight = 20;
        const paddingTop = 20;
        const paddingBottom = 60;

        const maxVal = Math.max(1, Math.max.apply(null, data));
        const chartWidth  = width - paddingLeft - paddingRight;
        const chartHeight = height - paddingTop - paddingBottom;

        ctx.clearRect(0, 0, width, height);
        ctx.font = '10px sans-serif';
        ctx.fillStyle = '#000';
        ctx.strokeStyle = '#000';

        // Y axis
        ctx.beginPath();
        ctx.moveTo(paddingLeft, paddingTop);
        ctx.lineTo(paddingLeft, height - paddingBottom);
        ctx.stroke();

        // X axis
        ctx.beginPath();
        ctx.moveTo(paddingLeft, height - paddingBottom);
        ctx.lineTo(width - paddingRight, height - paddingBottom);
        ctx.stroke();

        // Y ticks
        ctx.fillText('0', 8, height - paddingBottom + 12);
        ctx.fillText(String(maxVal), 8, paddingTop + 4);

        const n = labels.length;
        const stepX = chartWidth / Math.max(1, n);

        data.forEach((val, i) => {
            const barWidth = stepX * 0.7;
            const xCenter = paddingLeft + stepX * i + stepX / 2;
            const x = xCenter - barWidth / 2;

            const barHeight = (val / maxVal) * chartHeight;
            const y = height - paddingBottom - barHeight;

            // bar
            ctx.fillStyle = '#2563eb';
            ctx.fillRect(x, y, barWidth, barHeight);

            // x label rotated
            const label = String(labels[i]);
            ctx.save();
            ctx.translate(xCenter, height - paddingBottom + 30);
            ctx.rotate(-Math.PI / 4);
            ctx.textAlign = 'right';
            ctx.fillStyle = '#000';
            const truncated = label.length > 18 ? label.slice(0, 17) + '…' : label;
            ctx.fillText(truncated, 0, 0);
            ctx.restore();
        });
    })();
</script>

