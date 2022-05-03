<?php
namespace app\database\paginate;

class Paginate
{
    private int $perPage = 10;
    private string $urlIdentification = 'page';
    private array $data ;

    public function setPerPage(int $perPage)
    {
        $this->perPage = $perPage;
    }

    public function setUrlIdentification(string $urlIdentification)
    {
        $this->urlIdentification = $urlIdentification;
    }

    public function setData(array $data)
    {
        $this->data = $data;
    }

    private function calculations()
    {
        // total

        // offset

        // total pages
    }
}
