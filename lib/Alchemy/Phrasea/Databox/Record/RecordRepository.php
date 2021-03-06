<?php
/*
 * This file is part of Phraseanet
 *
 * (c) 2005-2016 Alchemy
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Alchemy\Phrasea\Databox\Record;

use Alchemy\Phrasea\Model\Entities\User;

interface RecordRepository
{
    /**
     * @param mixed    $record_id
     * @param null|int $number
     * @return null|\record_adapter
     */
    public function find($record_id, $number = null);

    /**
     * @param string $sha256
     * @return \record_adapter[]
     */
    public function findBySha256($sha256);

    /**
     * @param string $uuid
     * @return \record_adapter[]
     */
    public function findByUuid($uuid);

    /**
     * @param array $recordIds
     * @return \record_adapter[]
     */
    public function findByRecordIds(array $recordIds);

    /**
     * Find children of each given storyId reachable for given user
     *
     * @param int[] $storyIds
     * @param null|int|User $user
     * @return \set_selection[]
     */
    public function findChildren(array $storyIds, $user = null);


    /**
     * Find stories containing records
     *
     * @param int[] $recordIds
     * @param null|int|User $user
     * @return \set_selection[]
     */
    public function findParents(array $recordIds, $user = null);
}
