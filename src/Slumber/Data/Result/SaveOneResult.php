<?php
/**
 * Created by gerk on 05.11.17 17:33
 */

namespace PeekAndPoke\Component\Slumber\Data\Result;


/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class SaveOneResult extends InsertOneResult
{
    /** @var bool */
    private $upserted;

    /**
     * @param string $insertId
     * @param bool   $acknowledged
     * @param bool   $upserted
     */
    public function __construct($insertId, $acknowledged, $upserted)
    {
        parent::__construct($insertId, $acknowledged);

        $this->upserted = $upserted;
    }

    /**
     * @return bool
     */
    public function isUpserted()
    {
        return $this->upserted;
    }
}
