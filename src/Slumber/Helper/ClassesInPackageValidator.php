<?php
/**
 * File was created 16.10.2015 08:08
 */

namespace PeekAndPoke\Component\Slumber\Helper;

use Doctrine\Common\Annotations\AnnotationReader;
use PeekAndPoke\Component\Psi\Psi;
use PeekAndPoke\Component\Slumber\Core\Codec\ArrayCodecPropertyMarker2Mapper;
use PeekAndPoke\Component\Slumber\Core\LookUp\AnnotatedEntityConfigReader;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class ClassesInPackageValidator
{
    /** @var LoggerInterface */
    private $logger;
    /** @var null|ContainerInterface */
    private $provider;

    /**
     * ClassesInPackageValidator constructor.
     *
     * @param LoggerInterface|null    $logger
     * @param ContainerInterface|null $provider
     */
    public function __construct(LoggerInterface $logger = null, ContainerInterface $provider = null)
    {
        $this->logger   = $logger ?: new NullLogger();
        $this->provider = $provider;

//        AnnotationRegistry::registerLoader('class_exists');
    }

    /**
     * @param string|string[] $directories
     * @param string|string[] $excludeDirs
     *
     * @return string[]
     */
    public function validate($directories, array $excludeDirs = [])
    {
        $fqcns  = $this->getAllFqcnsInDir($directories, $excludeDirs);
        $lookUp = new AnnotatedEntityConfigReader(
            $this->provider ?: new UnitTestServiceProvider(),
            new AnnotationReader(),
            new ArrayCodecPropertyMarker2Mapper()
        );

        $errors = [];

        if (\count($fqcns) === 0) {
            $errors[] = 'No classes found in dirs ' . implode(', ', (array) $directories);
        }

        foreach ($fqcns as $fqcn) {

            try {
                $reflect = new \ReflectionClass($fqcn);

                $this->logger->info('validating ' . $fqcn);
                $lookUp->getEntityConfig($reflect);
                $this->logger->info('... OK');

            } catch (\Exception $e) {
                $msg      = $e->getMessage() . ' used in class ' . $fqcn;
                $errors[] = $msg;

                $this->logger->error($msg);
            }
        }

        return $errors;
    }

    /**
     * @param string|string[] $directories
     * @param string|string[] $excludeDirs
     *
     * @return string[]
     */
    private function getAllFqcnsInDir($directories, $excludeDirs)
    {
        $finder = (new Finder())->name('*.php')->in($directories);
        $fqcns  = [];

        /** @var SplFileInfo $item */
        foreach ($finder->getIterator() as $item) {

            // we have to exclude manually since it does not work on the finder when using exclude()
            $excludeIt = Psi::it($excludeDirs)
                ->filter(function ($dir) use ($item) {
                    return strpos($item->getRealPath(), $dir) === 0;
                })
                ->count();

            if ($excludeIt) {
                continue;
            }

            $tokens    = token_get_all($item->getContents());
            $namespace = '';

            for ($index = 0; isset($tokens[$index]); $index++) {
                if (! isset($tokens[$index][0])) {
                    continue;
                }
                if (T_NAMESPACE === $tokens[$index][0]) {
                    $index += 2; // Skip namespace keyword and whitespace
                    while (isset($tokens[$index]) && \is_array($tokens[$index])) {
                        $namespace .= $tokens[$index++][1];
                    }
                }

                if (T_CLASS === $tokens[$index][0] && \count($tokens[$index + 2]) > 1) {
                    $index += 2; // Skip class keyword and whitespace

                    $fqcn = $namespace . '\\' . $tokens[$index][1];

                    if (class_exists($fqcn)) {
                        $fqcns[] = $namespace . '\\' . $tokens[$index][1];
                    }
                }
            }
        }

        return $fqcns;
    }
}
