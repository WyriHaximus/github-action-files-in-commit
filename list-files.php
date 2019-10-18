<?php

use ApiClients\Client\Github\AsyncClient;
use ApiClients\Client\Github\Authentication\Token;
use ApiClients\Client\Github\Resource\Async\Repository;
use ApiClients\Client\Github\Resource\Async\Repository\Commit;
use ApiClients\Client\Github\Resource\Async\User;
use Bramus\Monolog\Formatter\ColoredLineFormatter;
use Monolog\Logger;
use React\EventLoop\Factory;
use React\Promise\Promise;
use React\Promise\PromiseInterface;
use Rx\Observable;
use WyriHaximus\Monolog\FormattedPsrHandler\FormattedPsrHandler;
use WyriHaximus\PSR3\CallableThrowableLogger\CallableThrowableLogger;
use WyriHaximus\React\PSR3\Stdio\StdioLogger;
use function ApiClients\Foundation\resource_pretty_print;
use function ApiClients\Tools\Rx\observableFromArray;
use function React\Promise\all;
use function React\Promise\resolve;
use function WyriHaximus\React\timedPromise;

require __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

const REPOSITORY = 'GITHUB_REPOSITORY';
const TOKEN = 'GITHUB_TOKEN';
const SHA = 'GITHUB_SHA';
const HEAD = 'INPUT_HEADSHA';
const BASE = 'INPUT_BASESHA';

(function () {
    $loop = Factory::create();
    $consoleHandler = new FormattedPsrHandler(StdioLogger::create($loop)->withHideLevel(true));
    $consoleHandler->setFormatter(new ColoredLineFormatter(
        null,
        '[%datetime%] %channel%.%level_name%: %message%',
        'Y-m-d H:i:s.u',
        true,
        false
    ));
    $logger = new Logger('wait');
    $logger->pushHandler($consoleHandler);
    [$owner, $repo] = explode('/', getenv(REPOSITORY));
    $logger->debug('Looking up owner: ' . $owner);
    AsyncClient::create($loop, new Token(getenv(TOKEN)))->user($owner)->then(function (User $user) use ($repo, $logger) {
        $logger->debug('Looking up repository: ' . $repo);
        return $user->repository($repo);
    })->then(function (Repository $repository) use ($logger) {
        if (getenv(BASE) !== false && strlen(getenv(BASE)) > 0 && getenv(HEAD) !== false && strlen(getenv(HEAD)) > 0) {
            return $repository->compareCommits(getenv(BASE), getenv(HEAD))->then(function (Repository\Compare $compare) {
                return $compare->files();
            });
        }

        $logger->debug('Locating commit: ' . getenv(SHA));
        return $repository->specificCommit(getenv(SHA))->then(function (Commit $commit) {
            return $commit->files();
        });
    })->then(function (array $commitFiles) use ($logger) {
        $logger->info('File count: ' . count($commitFiles));

        $files = [];

        foreach ($commitFiles as $file) {
            $files[] = $file->filename();
        }

        return $files;
    })->then(function (array $list) use ($loop) {
        return timedPromise($loop, 2, $list);
    })->done(function (array $list) {
        echo PHP_EOL, '::set-output name=files::' . implode(',', $list), PHP_EOL;
    }, CallableThrowableLogger::create($logger));
    $loop->run();
})();
