<?php

namespace App\Interfaces\Http\Controllers;

use App\Application\Chirp\Commands\CreateChirpCommand;
use App\Application\Chirp\Commands\DeleteChirpCommand;
use App\Application\Chirp\Commands\UpdateChirpCommand;
use App\Application\Chirp\Handlers\Command\CreateChirpCommandHandler;
use App\Application\Chirp\Handlers\Command\DeleteChirpCommandHandler;
use App\Application\Chirp\Handlers\Command\UpdateChirpCommandHandler;
use App\Application\Chirp\Handlers\Query\ListUserChirpsQueryHandler;
use App\Application\Chirp\Queries\ListUserChirpsQuery;
use App\Application\Chirp\DTOs\ChirpView;
use App\Http\Controllers\Controller;
use App\Interfaces\Http\Auth\CurrentUserProvider;
use App\Interfaces\Http\Requests\Chirp\DeleteChirpRequest;
use App\Interfaces\Http\Requests\Chirp\StoreChirpRequest;
use App\Interfaces\Http\Requests\Chirp\UpdateChirpRequest;
use App\Models\Chirp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Inertia\Inertia;
use Inertia\Response;

class ChirpController extends Controller
{
    public function __construct(
        private readonly CurrentUserProvider $currentUserProvider,
        private readonly ListUserChirpsQueryHandler $listUserChirpsQueryHandler,
        private readonly CreateChirpCommandHandler $createChirpCommandHandler,
        private readonly UpdateChirpCommandHandler $updateChirpCommandHandler,
        private readonly DeleteChirpCommandHandler $deleteChirpCommandHandler,
    ) {
    }

    /**
     * Show the chirp management page.
     */
    public function index(): Response
    {
        $chirps = $this->listUserChirpsQueryHandler->handle(
            new ListUserChirpsQuery($this->currentUserProvider->id()),
        );

        return Inertia::render('jumper/index', [
            'chirps' => $chirps->map(
                fn (ChirpView $chirp): array => $chirp->toArray(),
            )->values(),
        ]);
    }

    /**
     * Store a newly created chirp.
     */
    public function store(StoreChirpRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $imagePath = $request->file('image') instanceof UploadedFile
            ? $request->file('image')->store('chirps', 'public')
            : null;

        $this->createChirpCommandHandler->handle(new CreateChirpCommand(
            userId: $request->user()->id,
            title: $validated['title'],
            description: $validated['description'] ?? null,
            imagePath: $imagePath,
        ));

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Jumper creado correctamente.']);

        return to_route('jumper.index');
    }

    /**
     * Update an existing chirp owned by the authenticated user.
     */
    public function update(UpdateChirpRequest $request, Chirp $chirp): RedirectResponse
    {
        $validated = $request->validated();

        $this->updateChirpCommandHandler->handle(new UpdateChirpCommand(
            userId: $request->user()->id,
            chirpId: $chirp->id,
            title: $validated['title'],
            description: $validated['description'] ?? null,
        ));

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Jumper actualizado correctamente.']);

        return to_route('jumper.index');
    }

    /**
     * Delete a chirp owned by the authenticated user.
     */
    public function destroy(DeleteChirpRequest $request, Chirp $chirp): RedirectResponse
    {
        $this->deleteChirpCommandHandler->handle(new DeleteChirpCommand(
            userId: $request->user()->id,
            chirpId: $chirp->id,
        ));

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Jumper eliminado correctamente.']);

        return to_route('jumper.index');
    }
}
