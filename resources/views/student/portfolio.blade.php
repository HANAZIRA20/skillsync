@extends('layouts.app')

@section('title', 'Portfolio')
@section('page-title', 'Portfolio')
@section('page-subtitle', 'Showcase karya terbaik Anda untuk para client')

@section('content')
<div class="space-y-6 animate-fade-in">

    {{-- Public Link Banner --}}
    <div class="card p-5 flex items-center gap-4">
        <div class="w-10 h-10 bg-accent-500/20 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-link text-accent-400"></i>
        </div>
        <div class="flex-1">
            <p class="text-sm font-semibold text-white">Link Portfolio Publik</p>
            <p class="text-xs text-gray-500 mt-0.5">Bagikan link ini kepada client atau rekruter</p>
        </div>
        <div class="flex items-center gap-2">
            <code class="text-xs bg-white/5 px-3 py-1.5 rounded-lg text-gray-400 border border-white/5">
                {{ url('/portfolio/' . auth()->id()) }}
            </code>
            <button onclick="copyLink()" class="btn-outline px-3 py-1.5 rounded-xl text-xs font-medium">
                <i class="fas fa-copy mr-1"></i>Copy
            </button>
        </div>
    </div>

    {{-- Portfolio Grid --}}
    @if(empty($portfolios) || count($portfolios) === 0)
    <div class="card p-16 text-center">
        <div class="w-20 h-20 bg-white/5 rounded-2xl flex items-center justify-center mx-auto mb-5">
            <i class="fas fa-star text-3xl text-gray-600"></i>
        </div>
        <p class="text-lg font-semibold text-gray-400">Portfolio kosong</p>
        <p class="text-sm text-gray-600 mt-1 mb-5">Selesaikan proyek untuk menambah portfolio Anda</p>
        <a href="{{ route('student.projects') }}" class="btn-primary inline-block px-5 py-2.5 rounded-xl text-sm font-semibold">
            Lihat Proyek Saya
        </a>
    </div>
    @else
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($portfolios as $portfolio)
        <div class="card overflow-hidden group">
            {{-- Card Header --}}
            <div class="h-36 bg-gradient-to-br
                @php
                    $gradients = ['from-primary-900/60 to-purple-900/60', 'from-accent-900/60 to-teal-900/60', 'from-rose-900/60 to-pink-900/60', 'from-amber-900/60 to-yellow-900/60'];
                    echo $gradients[$loop->index % count($gradients)];
                @endphp
                flex items-center justify-center relative overflow-hidden">
                <i class="fas fa-code text-4xl text-white/20"></i>
                <div class="absolute top-3 right-3">
                    @if($portfolio->is_public)
                    <span class="badge badge-open">Publik</span>
                    @else
                    <span class="badge badge-review">Private</span>
                    @endif
                </div>
            </div>

            <div class="p-5">
                <h3 class="text-sm font-bold text-white mb-1 group-hover:text-primary-300 transition-colors">
                    {{ $portfolio->project?->title ?? 'Proyek' }}
                </h3>
                @if($portfolio->project?->category)
                <span class="text-[10px] px-2 py-0.5 bg-primary-500/10 text-primary-400 rounded-full border border-primary-500/20">
                    {{ $portfolio->project->category }}
                </span>
                @endif

                @if($portfolio->description)
                <p class="text-xs text-gray-500 mt-3 line-clamp-2">{{ $portfolio->description }}</p>
                @endif

                @if($portfolio->tech_stack && count($portfolio->tech_stack) > 0)
                <div class="flex flex-wrap gap-1 mt-3">
                    @foreach(array_slice($portfolio->tech_stack, 0, 4) as $tech)
                    <span class="text-[10px] px-2 py-0.5 bg-white/5 text-gray-400 rounded-full border border-white/5">{{ $tech }}</span>
                    @endforeach
                </div>
                @endif

                <div class="flex items-center justify-between mt-4 pt-4 border-t border-white/5">
                    @if($portfolio->rating_received)
                    <div class="flex items-center gap-1">
                        @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star text-xs {{ $i <= $portfolio->rating_received ? 'text-yellow-400' : 'text-gray-700' }}"></i>
                        @endfor
                    </div>
                    @else
                    <span class="text-xs text-gray-600">Belum ada rating</span>
                    @endif

                    <form action="{{ route('student.portfolio.toggle', $portfolio->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="text-xs {{ $portfolio->is_public ? 'text-gray-500 hover:text-red-400' : 'text-primary-400 hover:text-primary-300' }} transition-colors">
                            <i class="fas fa-{{ $portfolio->is_public ? 'eye-slash' : 'eye' }} mr-1"></i>
                            {{ $portfolio->is_public ? 'Sembunyikan' : 'Tampilkan' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
function copyLink() {
    navigator.clipboard.writeText('{{ url('/portfolio/' . auth()->id()) }}').then(() => {
        alert('Link berhasil disalin!');
    });
}
</script>
@endpush
