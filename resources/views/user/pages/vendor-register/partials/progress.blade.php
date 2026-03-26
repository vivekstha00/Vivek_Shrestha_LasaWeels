@php
    $steps = [
        1 => 'Account',
        2 => 'Personal',
        3 => 'Business',
        4 => 'Documents',
        5 => 'Review',
    ];
@endphp

<div class="card border-0 rounded-4 shadow-sm">
    <div class="card-body py-4">
        <div class="d-flex justify-content-between flex-wrap gap-3">
            @foreach($steps as $number => $label)
                <div class="text-center flex-fill">
                    <div class="step-circle
                        @if($currentStep > $number) step-done
                        @elseif($currentStep == $number) step-active
                        @else step-pending
                        @endif">
                        @if($currentStep > $number)
                            ✓
                        @else
                            {{ $number }}
                        @endif
                    </div>
                    <div class="mt-2 small fw-semibold">{{ $label }}</div>
                </div>
            @endforeach
        </div>
    </div>
</div>
