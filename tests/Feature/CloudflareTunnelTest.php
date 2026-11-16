<?php
namespace Tests\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class CloudflareTunnelTest extends TestCase {
    use RefreshDatabase;
    protected function setUp(): void { parent::setUp(); $this->seed(); }
    public function test_url_css_https_saat_lewat_tunnel(): void {
        // Simulasi request yang diteruskan cloudflared: koneksi lokal HTTP,
        // tapi header X-Forwarded-Proto: https dan X-Forwarded-Host: domain tunnel.
        $resp = $this->call('GET', '/', [], [], [], [
            'HTTP_X_FORWARDED_PROTO' => 'https',
            'HTTP_X_FORWARDED_HOST' => 'contoh-tunnel.trycloudflare.com',
            'HTTP_HOST' => '127.0.0.1:8002',
            'REMOTE_ADDR' => '127.0.0.1',
        ]);
        $resp->assertOk();
        $html = $resp->getContent();
        // CSS hasil build Vite harus dirujuk dengan https + host tunnel, bukan http.
        $this->assertStringContainsString('https://contoh-tunnel.trycloudflare.com/build/assets/', $html);
        $this->assertStringNotContainsString('http://127.0.0.1:8002/build/assets/', $html);
        $this->assertStringNotContainsString('http://contoh-tunnel.trycloudflare.com/build/', $html);
    }
    public function test_tanpa_tunnel_tetap_http_biasa(): void {
        $resp = $this->get('/');
        $resp->assertOk();
        $this->assertStringContainsString('/build/assets/', $resp->getContent());
    }
}
