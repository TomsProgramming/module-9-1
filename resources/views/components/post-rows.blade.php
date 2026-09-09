@foreach ($posts->chunk(3) as $row)
    <div class="lg:grid lg:grid-cols-3 lg:gap-6 space-y-6 lg:space-y-0">
        @foreach ($row as $post)
            <x-post-card :post="$post" />
        @endforeach
    </div>
@endforeach
