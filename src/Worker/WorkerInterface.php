<?php
namespace StdLib\Worker;

/**
 * Standard interface for workers. Loading workers
 * via the application will not work unless they
 * implement this interface.
 *
 * Interface WorkerInterface
 * @package StdLib\Worker
 */
interface WorkerInterface
{
    /**
     * Start the worker process
     *
     * @return void
     */
    public function start();
    /**
     * Worker process running
     *
     * @return void
     */
    public function run();
    /**
     * Handle shutdown procedure
     *
     * @return void
     */
    public function shutdown();
    /**
     * Stop worker
     *
     * @return void
     */
    public function stop();
}
