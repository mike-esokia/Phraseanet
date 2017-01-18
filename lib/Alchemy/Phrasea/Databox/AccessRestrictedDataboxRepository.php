<?php

namespace Alchemy\Phrasea\Databox;

use Alchemy\Phrasea\Core\Configuration\AccessRestriction;

class AccessRestrictedDataboxRepository implements DataboxRepository
{

    /**
     * @var DataboxRepository
     */
    private $databoxRepository;

    /**
     * @var AccessRestriction
     */
    private $accessRestrictions;

    public function __construct(DataboxRepository $databoxRepository, AccessRestriction $accessRestriction)
    {
        $this->databoxRepository = $databoxRepository;
        $this->accessRestrictions = $accessRestriction;
    }

    /**
     * @param int $id
     * @return \databox
     */
    public function find($id)
    {
        $databox = $this->databoxRepository->find($id);

        if ($this->accessRestrictions->isDataboxAvailable($databox)) {
            return $databox;
        }

        return null;
    }

    /**
     * @return \databox[]
     */
    public function findAll()
    {
        return $this->accessRestrictions->filterAvailableDataboxes($this->databoxRepository->findAll());
    }

    /**
     * @param \databox $databox
     */
    public function save(\databox $databox)
    {
        if (! $this->accessRestrictions->isDataboxAvailable($databox)) {
            return false;
        }

        return $this->databoxRepository->save($databox);
    }

    /**
     * @param \databox $databox
     */
    public function delete(\databox $databox)
    {
        if (! $this->accessRestrictions->isDataboxAvailable($databox)) {
            return false;
        }

        return $this->databoxRepository->delete($databox);
    }

    /**
     * @param \databox $databox
     */
    public function unmount(\databox $databox)
    {
        if (! $this->accessRestrictions->isDataboxAvailable($databox)) {
            return false;
        }

        return $this->databoxRepository->unmount($databox);
    }

    /**
     * @param $host
     * @param $port
     * @param $user
     * @param $password
     * @param $dbname
     *
     * @return \databox
     */
    public function mount($host, $port, $user, $password, $dbname)
    {
        return $this->databoxRepository->mount($host, $port, $user, $password, $dbname);
    }

    /**
     * @param $host
     * @param $port
     * @param $user
     * @param $password
     * @param $dbname
     *
     * @return \databox
     */
    public function create($host, $port, $user, $password, $dbname)
    {
        return $this->databoxRepository->create($host, $port, $user, $password, $dbname);
    }
}
