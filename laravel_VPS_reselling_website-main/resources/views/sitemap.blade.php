{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- Main Public Landing Pages -->
    <url>
        <loc>{{ url('/') }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>{{ url('/plans') }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>

    <!-- Legal & Compliance Pages -->
    <url>
        <loc>{{ route('legal.terms') }}</loc>
        <lastmod>{{ now()->startOfMonth()->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    <url>
        <loc>{{ route('legal.privacy') }}</loc>
        <lastmod>{{ now()->startOfMonth()->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    <url>
        <loc>{{ route('legal.cookies') }}</loc>
        <lastmod>{{ now()->startOfMonth()->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.4</priority>
    </url>

    <!-- Active VPS Packages -->
    @foreach($packages as $package)
    <url>
        <loc>{{ route('checkout.show', $package) }}</loc>
        <lastmod>{{ $package->updated_at ? $package->updated_at->toAtomString() : now()->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach
</urlset>
