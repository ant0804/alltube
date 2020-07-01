<?php

namespace Alltube\Robo\Plugin\Commands;

use Robo\Tasks;

/**
 * Manage robo tasks.
 */
class ReleaseCommand extends Tasks
{

    /**
     * Create release archive
     * @return void
     */
    public function release()
    {
        $this->stopOnFail();

        $result = $this->taskExec('git')
            ->arg('describe')
            ->run();
        $result->provideOutputdata();

        $tmpDir = $this->_tmpDir();

        $filename = 'alltube-' . trim((string) $result->getOutputData()) . '.zip';

        $this->taskFilesystemStack()
            ->remove($filename)
            ->run();

        $this->taskGitStack()
            ->cloneRepo(__DIR__ . '/../../../../', $tmpDir)
            ->run();

        $this->taskComposerInstall()
            ->dir($tmpDir)
            ->optimizeAutoloader()
            ->noDev()
            ->run();

        $this->taskPack($filename)
            ->addDir('alltube', $tmpDir)
            ->run();
    }
}
