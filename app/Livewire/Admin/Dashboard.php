<?php
// app/Livewire/Admin/Dashboard.php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Domains\Content\Models\Article;
use App\Domains\Content\Models\Comment;
use App\Models\NewsletterSubscriber;
use App\Domains\Content\Models\Category;
use App\Domains\Content\Models\Tag;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
class Dashboard extends Component
{
    public $periodo = '30_dias';
    public $metricas = [];
    public $graficos = [];
    public $atividadeRecente = [];
    public $topContent = [];
    public $atividadeComunidade = [];

    protected $queryString = ['periodo'];

    public function mount()
    {
        $this->carregarMetricas();
        $this->carregarGraficos();
        $this->carregarAtividadeRecente();
        $this->carregarConteudoTop();
        $this->carregarAtividadeComunidade();
    }

    public function updatedPeriodo()
    {
        $this->carregarMetricas();
        $this->carregarGraficos();
        $this->carregarConteudoTop();
        $this->carregarAtividadeComunidade();
    }

    public function carregarMetricas()
    {
        $cacheKey = "dashboard.metricas.{$this->periodo}";
        
        $this->metricas = Cache::remember($cacheKey, now()->addMinutes(5), function () {
            $periodo = $this->obterIntervaloPeriodo();
            
            return [
                // Conteúdo Editorial
                'artigos_publicados' => Article::where('status', 'published')->count(),
                'artigos_hoje' => Article::whereDate('published_at', today())->count(),
                'rascunhos' => Article::where('status', 'draft')->count(),
                'agendados' => Article::where('status', 'scheduled')->count(),
                
                // Performance
                'visualizacoes_total' => Article::sum('views_count'),
                'visualizacoes_periodo' => Article::whereBetween('published_at', [$periodo['inicio'], $periodo['fim']])->sum('views_count'),
                'media_visualizacoes' => round(Article::avg('views_count'), 0),
                'tempo_leitura_medio' => round(Article::avg('reading_time'), 0),
                
                // Comunidade
                'comentarios_total' => Comment::count(),
                'comentarios_pendentes' => Comment::where('status', 'pending')->count(),
                'comentarios_aprovados' => Comment::where('status', 'approved')->count(),
                'comentarios_spam' => Comment::where('status', 'spam')->count(),
                
                // Assinantes
                'assinantes_total' => NewsletterSubscriber::where('is_active', true)->count(),
                'novos_assinantes' => NewsletterSubscriber::whereBetween('created_at', [$periodo['inicio'], $periodo['fim']])->count(),
                
                // Categorização
                'total_categorias' => Category::count(),
                'total_tags' => Tag::count(),
            ];
        });
    }

    public function carregarGraficos()
    {
        $this->graficos = [
            'visualizacoes' => $this->getDadosVisualizacoes(),
            'categorias' => $this->getDadosCategorias(),
            'tipos_conteudo' => $this->getDadosTiposConteudo(),
            'comentarios' => $this->getDadosComentarios(),
        ];
    }

    public function carregarAtividadeRecente()
    {
        $this->atividadeRecente = [
            'comentarios' => Comment::with('commentable')
                ->latest()
                ->limit(8)
                ->get()
                ->map(fn($c) => [
                    'id' => $c->id,
                    'conteudo' => $c->content,
                    'autor' => $c->name,
                    'artigo' => $c->commentable->title ?? 'Artigo removido',
                    'quando' => $c->created_at->diffForHumans(),
                    'status' => $c->status,
                ]),
            
            'publicacoes' => Article::with('author')
                ->where('status', 'published')
                ->latest('published_at')
                ->limit(5)
                ->get()
                ->map(fn($a) => [
                    'id' => $a->id,
                    'titulo' => $a->title,
                    'autor' => $a->author->name,
                    'conteudo' => $a->content,
                    'visualizacoes' => $a->views_count,
                    'publicado' => $a->published_at->diffForHumans(),
                ]),
        ];
    }

    public function carregarConteudoTop()
    {
        $this->topContent = [
            'mais_lidos_semana' => Article::where('status', 'published')
                ->where('published_at', '>=', now()->subDays(7))
                ->orderBy('views_count', 'desc')
                ->limit(5)
                ->get()
                ->map(fn($a) => [
                    'titulo' => $a->title,
                    'visualizacoes' => $a->views_count,
                    'autor' => $a->author->name,
                    'published_at' => $a->published_at->format('d/m/Y'),
                ]),

            'mais_lidos_mes' => Article::where('status', 'published')
                ->where('published_at', '>=', now()->subDays(30))
                ->orderBy('views_count', 'desc')
                ->limit(5)
                ->get()
                ->map(fn($a) => [
                    'titulo' => $a->title,
                    'visualizacoes' => $a->views_count,
                    'autor' => $a->author->name,
                    'published_at' => $a->published_at->format('d/m/Y'),
                ]),
            
            'mais_comentados' => Article::withCount('comments')
                ->where('status', 'published')
                ->orderBy('comments_count', 'desc')
                ->limit(5)
                ->get()
                ->map(fn($a) => [
                    'titulo' => $a->title,
                    'comentarios' => $a->comments_count,
                    'autor' => $a->author->name,
                    'published_at' => $a->published_at->format('d/m/Y'),
                ]),
            
            'autores_top' => User::withCount('articles')
                ->orderBy('articles_count', 'desc')
                ->limit(5)
                ->get()
                ->map(fn($u) => [
                    'nome' => $u->name,
                    'artigos' => $u->articles_count,
                    'visualizacoes' => $u->articles()->sum('views_count'),
                ]),
        ];
    }

    public function carregarAtividadeComunidade()
    {
        $this->atividadeComunidade = [
            'total_leitores_engajados' => Comment::distinct('email')->count('email'),
            'media_comentarios_artigo' => round(Article::has('comments')->withCount('comments')->get()->avg('comments_count') ?? 0, 1),
            'horario_pico' => $this->getHorarioPico(),
            'taxa_engajamento' => $this->calcularTaxaEngajamento(),
        ];
    }

    private function getDadosVisualizacoes()
    {
        $dias = $this->periodo === '7_dias' ? 7 : ($this->periodo === '90_dias' ? 90 : 30);
        $dados = [];
        
        for ($i = $dias - 1; $i >= 0; $i--) {
            $data = now()->subDays($i);
            $dados[] = [
                'data' => $data->format('d/m'),
                'valor' => Article::whereDate('published_at', $data)->sum('views_count'),
            ];
        }
        
        return $dados;
    }

    private function getDadosCategorias()
    {
        return Category::withCount('articles')
            ->having('articles_count', '>', 0)
            ->orderBy('articles_count', 'desc')
            ->limit(8)
            ->get()
            ->filter(fn($c) => $c !== null && $c->name !== null)
            ->map(fn($c) => [
                'nome' => $c->name ?? 'Sem nome',
                'quantidade' => $c->articles_count ?? 0,
                'cor' => !empty($c->color) ? $c->color : '#10b981',
            ])
            ->values();
    }

    private function getDadosTiposConteudo()
    {
        $tipos = [
            ['rotulo' => 'Artigos', 'chave' => 'article', 'cor' => '#10b981'],
            ['rotulo' => 'Notícias', 'chave' => 'news', 'cor' => '#3b82f6'],
            ['rotulo' => 'Vídeos', 'chave' => 'video', 'cor' => '#8b5cf6'],
            ['rotulo' => 'Jornal', 'chave' => 'newspaper', 'cor' => '#f59e0b'],
        ];
        
        return collect($tipos)->map(fn($t) => [
            'rotulo' => $t['rotulo'],
            'valor' => Article::where('type', $t['chave'])->count(),
            'cor' => $t['cor'],
        ])->filter(fn($t) => $t['valor'] > 0)->values();
    }

    private function getDadosComentarios()
    {
        $dias = 7;
        $dados = [];
        
        for ($i = $dias - 1; $i >= 0; $i--) {
            $data = now()->subDays($i);
            $dados[] = [
                'data' => $data->format('d/m'),
                'quantidade' => Comment::whereDate('created_at', $data)->count(),
            ];
        }
        
        return $dados;
    }

    private function getHorarioPico()
    {
        $hora = Comment::select(DB::raw('HOUR(created_at) as hora'), DB::raw('count(*) as total'))
            ->groupBy('hora')
            ->orderBy('total', 'desc')
            ->first();
            
        return $hora ? sprintf('%02d:00 - %02d:00', $hora->hora, $hora->hora + 1) : 'Não disponível';
    }

    private function calcularTaxaEngajamento()
    {
        $totalVisualizacoes = Article::sum('views_count');
        $totalComentarios = Comment::count();
        
        return $totalVisualizacoes > 0 
            ? round(($totalComentarios / $totalVisualizacoes) * 100, 2)
            : 0;
    }

    private function obterIntervaloPeriodo()
    {
        $fim = now();
        
        $inicio = match($this->periodo) {
            '7_dias' => now()->subDays(7),
            '30_dias' => now()->subDays(30),
            '90_dias' => now()->subDays(90),
            default => now()->subDays(30),
        };
        
        return ['inicio' => $inicio, 'fim' => $fim];
    }

    public function aprovarComentario($id)
    {
        $comentario = Comment::find($id);
        if ($comentario) {
            $comentario->update(['status' => 'approved']);
            $this->carregarAtividadeRecente();
            $this->dispatch('notificacao', 'Comentário aprovado com sucesso');
        }
    }

    public function marcarComentarioSpam($id)
    {
        $comentario = Comment::find($id);
        if ($comentario) {
            $comentario->update(['status' => 'spam']);
            $this->carregarAtividadeRecente();
            $this->dispatch('notificacao', 'Comentário marcado como spam');
        }
    }

    public function render()
    {
        return view('livewire.admin.dashboard')
            ->layout('layouts.admin');
    }
}