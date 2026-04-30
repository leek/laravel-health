<?php

namespace Spatie\Health\Checks\Checks;

use Exception;
use Laravel\Octane\RoadRunner\ServerProcessInspector as RoadRunnerServerProcessInspector;
use Laravel\Octane\Swoole\ServerProcessInspector as SwooleServerProcessInspector;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

class OctaneCheck extends Check
{
    protected ?string $server = null;

    protected ?string $name = 'Octane';

    public function server(string $server): self
    {
        $this->server = $server;

        return $this;
    }

    public function run(): Result
    {
        $result = Result::make();

        try {
            $server = $this->server ?: config('octane.server', 'swoole');

            $isRunning = match ($server) {
                'swoole' => $this->isSwooleServerRunning(),
                'roadrunner' => $this->isRoadRunnerServerRunning(),
                default => null,
            };
        } catch (Exception) {
            return $result
                ->failed('Octane does not seem to be installed correctly.')
                ->shortSummary('Not installed');
        }

        if ($isRunning === null) {
            return $result
                ->failed("Octane server `{$server}` is not supported.")
                ->shortSummary('Invalid server');
        }

        if (! $isRunning) {
            return $result
                ->failed('Octane server is not running.')
                ->shortSummary('Not running');
        }

        return $result
            ->ok()
            ->shortSummary('Running');
    }

    protected function isSwooleServerRunning(): bool
    {
        return app(SwooleServerProcessInspector::class)->serverIsRunning();
    }

    protected function isRoadRunnerServerRunning(): bool
    {
        return app(RoadRunnerServerProcessInspector::class)->serverIsRunning();
    }
}
