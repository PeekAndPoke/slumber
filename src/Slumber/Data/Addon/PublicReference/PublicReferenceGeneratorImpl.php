<?php
/**
 * Created by IntelliJ IDEA.
 * User: gerk
 * Date: 14.04.17
 * Time: 22:40
 */

namespace PeekAndPoke\Component\Slumber\Data\Addon\PublicReference;

use PeekAndPoke\Component\Slumber\Data\Error\DuplicateError;
use PeekAndPoke\Component\Slumber\Data\Repository;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class PublicReferenceGeneratorImpl implements PublicReferenceGenerator
{
    public static $REDUCED_UPPER_CHARS = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'K', 'L', 'M', 'N', 'P', 'R', 'S', 'T', 'V', 'W', 'X', 'Y', 'Z'];

    /** @var Repository */
    private $repository;
    /** @var string */
    private $pattern;
    /** @var array */
    private $cls2pattern;

    /**
     * PublicReferenceGeneratorImpl constructor.
     *
     * @param Repository $repository
     * @param string     $pattern
     * @param array      $cls2pattern
     */
    public function __construct(Repository $repository, $pattern = '$$-__-########', array $cls2pattern = [])
    {
        $this->repository  = $repository;
        $this->pattern     = $pattern;
        $this->cls2pattern = $cls2pattern;
    }

    /**
     * @param mixed $subject The object to create a public unique reference for
     *
     * @return null|string
     */
    public function create($subject)
    {
        if (! \is_object($subject)) {
            return null;
        }

        $reflect = new \ReflectionClass($subject);
        $pattern = $this->cls2pattern[$reflect->name] ?? $this->pattern;
        $tries   = 1000;

        while (--$tries >= 0) {

            $new = $this->generate($pattern, $reflect->getShortName());

            try {
                $this->repository->insert((new PublicReference())->setId($new));

                return $new;

            } catch (DuplicateError $e) {
            }
        }

        throw new \RuntimeException('Could not generate a unique public reference');
    }


    /**
     * @param string $pattern
     * @param string $className
     *
     * @return string
     */
    private function generate($pattern, $className)
    {
        $ret           = '';
        $classNameIdx  = 0;
        $patternLength = \strlen($pattern);

        for ($i = 0; $i < $patternLength; $i++) {

            $char = $pattern[$i];

            switch ($char) {
                case '_':
                    $ret .= self::$REDUCED_UPPER_CHARS[random_int(0, \count(static::$REDUCED_UPPER_CHARS) - 1)];
                    break;

                case '#':
                    $ret .= random_int(0, 9);
                    break;

                case '$':
                    $ret .= strtoupper($className[$classNameIdx++]);
                    break;

                default:
                    $ret .= $char;
                    break;
            }
        }

        return $ret;
    }
}
