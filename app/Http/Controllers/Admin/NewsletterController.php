<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Newsletter;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NewsletterController extends Controller
{
    public function index()
    {
        $newsletters = Newsletter::with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $stats = [
            'total' => Newsletter::count(),
            'drafts' => Newsletter::where('status', 'draft')->count(),
            'scheduled' => Newsletter::where('status', 'scheduled')->count(),
            'sent' => Newsletter::where('status', 'sent')->count(),
            'subscribers' => NewsletterSubscriber::active()->count(),
        ];

        return view('admin.newsletters.index', compact('newsletters', 'stats'));
    }

    public function create()
    {
        return view('admin.newsletters.form', [
            'newsletter' => new Newsletter(),
            'subscribersCount' => NewsletterSubscriber::active()->count()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $newsletter = Newsletter::create([
            'subject' => $validated['subject'],
            'content' => $validated['content'],
            'status' => $request->has('schedule') ? 'scheduled' : 'draft',
            'scheduled_at' => $validated['scheduled_at'] ?? null,
            'created_by' => Auth::id(),
        ]);

        if ($request->has('schedule')) {
            return redirect()->route('admin.newsletters.index')
                ->with('success', 'Newsletter agendada com sucesso!');
        }

        return redirect()->route('admin.newsletters.edit', $newsletter)
            ->with('success', 'Rascunho salvo com sucesso!');
    }

    public function edit(Newsletter $newsletter)
    {
        return view('admin.newsletters.form', [
            'newsletter' => $newsletter,
            'subscribersCount' => NewsletterSubscriber::active()->count()
        ]);
    }

    public function update(Request $request, Newsletter $newsletter)
    {
        if ($newsletter->isSent()) {
            return back()->with('error', 'Não é possível editar uma newsletter já enviada.');
        }

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $newsletter->update([
            'subject' => $validated['subject'],
            'content' => $validated['content'],
            'status' => $request->has('schedule') ? 'scheduled' : 'draft',
            'scheduled_at' => $validated['scheduled_at'] ?? null,
        ]);

        $message = $request->has('schedule')
            ? 'Newsletter agendada com sucesso!'
            : 'Rascunho atualizado com sucesso!';

        return redirect()->route('admin.newsletters.index')
            ->with('success', $message);
    }

    public function destroy(Newsletter $newsletter)
    {
        if (!$newsletter->isDraft()) {
            return back()->with('error', 'Apenas rascunhos podem ser excluídos.');
        }

        $newsletter->delete();

        return redirect()->route('admin.newsletters.index')
            ->with('success', 'Newsletter excluída com sucesso!');
    }

    public function send(Newsletter $newsletter)
    {
        if (!$newsletter->isDraft() && !$newsletter->isScheduled()) {
            return back()->with('error', 'Esta newsletter não pode ser enviada.');
        }

        $subscribersCount = NewsletterSubscriber::active()->count();

        if ($subscribersCount === 0) {
            return back()->with('error', 'Não há assinantes ativos para enviar a newsletter.');
        }

        // Atualiza status para sending
        $newsletter->update(['status' => 'sending']);

        // Dispara job para processar envios
        dispatch(new \App\Jobs\ProcessNewsletterJob($newsletter));

        return redirect()->route('admin.newsletters.index')
            ->with('success', 'Envio da newsletter iniciado! ' . $subscribersCount . ' emails serão processados.');
    }

    public function subscribers(Request $request)
    {
        $query = NewsletterSubscriber::orderBy('created_at', 'desc');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $subscribers = $query->paginate(15);

        return view('admin.newsletters.subscribers', compact('subscribers'));
    }

    public function destroySubscriber(NewsletterSubscriber $subscriber)
    {
        $subscriber->delete();

        return redirect()->route('admin.newsletters.subscribers')
            ->with('success', 'Assinante removido com sucesso!');
    }

    public function exportSubscribers()
    {
        $subscribers = NewsletterSubscriber::active()->get(['email', 'name', 'subscribed_at']);

        $filename = 'assinantes-newsletter-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($subscribers) {
            $file = fopen('php://output', 'w');

            // Cabeçalhos
            fputcsv($file, ['Email', 'Nome', 'Data de inscrição']);

            // Dados
            foreach ($subscribers as $subscriber) {
                fputcsv($file, [
                    $subscriber->email,
                    $subscriber->name ?? '-',
                    $subscriber->subscribed_at?->format('d/m/Y H:i')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function show(Newsletter $newsletter)
    {
        $deliveries = $newsletter->deliveries()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total' => $newsletter->deliveries()->count(),
            'sent' => $newsletter->deliveries()->sent()->count(),
            'pending' => $newsletter->deliveries()->pending()->count(),
            'failed' => $newsletter->deliveries()->failed()->count(),
        ];

        return view('admin.newsletters.show', compact('newsletter', 'deliveries', 'stats'));
    }
}
