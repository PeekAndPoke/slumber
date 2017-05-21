<?php
/**
 * Created by IntelliJ IDEA.
 * User: gerk
 * Date: 26.01.17
 * Time: 09:49
 */

namespace PeekAndPoke\Component\Slumber\FrameworkIntegration;


/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
interface StorageProfiler
{
    /**
     * @param string $name
     * @param array  $data
     *
     * @return StorageProfiler\RunningSample
     */
    public function start($name, array $data);

    /**
     * @param StorageProfiler\RunningSample $sample
     */
    public function stop(StorageProfiler\RunningSample $sample);
}
