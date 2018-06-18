<?php
declare(strict_types=1);

class schoolCollection {
    protected $data    = [];
    protected $idxCode = [];
    protected $idxName = [];

    public    $webSrc  = '';
    protected $fileSrc = 'tmp/data.json';
    protected $dataSrc = false;

    public function load() {
        
        if (false === file_exists($this->fileSrc)) {
            $this->dataSrc = file_get_contents($this->webSrc);
            if (empty($this->dataSrc)) {
                throw new \Exception('Failed to fetch file data from `'.$this->webSrc.'`');
            }

            if (false === file_put_contents($this->fileSrc, $this->dataSrc)) {
                throw new \Exception('Failed to save data to `'.$this->fileSrc.'`');
            }
        }
        
        if ($this->dataSrc === false) {
            $this->dataSrc = file_get_contents($this->fileSrc);
        }
        
        if ($this->dataSrc === false) {
            throw new \Exception('Failed to load `'.$this->fileSrc.'`');
        }
        
        $data = json_decode($this->dataSrc);
        if (empty($data)) {
            unlink($this->fileSrc);
            throw new \Exception('Failed to parse data. Err: '.json_last_error_msg());
        }

        $numSchools = count($data);
        foreach ($data as $idx => $srcSchool) {
            $obj = \school::createFromSrc($srcSchool);
            assert($obj instanceOf \school);
            $this->addSchool($obj);
        }

        return $this->getCount();
    }

    public function getCount() {
        return count($this->data);
    }

    public function addSchool(school $school) {
        $idx = (array_push($this->data, $school) - 1);
        $this->idxCode[$school->code] = $this->idxName[strtolower($school->name)] = $idx;
    }

    public function getByCode(int $code) {
        if (false === isset($this->idxCode[$code])) {
            throw new \Exception('No school found matching code `'.$code.'`');
        }
        return $this->data[$this->idxCode[$code]];
    }

    public function getByName(string $name) {
        $name = strtolower($name);
        if (false === isset($this->idxName[$name])) {
            throw new \Exception('No school found matching name `'.$name.'`');
        }
        return $this->data[$this->idxName[$name]];
    }

};

class school {
    var $name;
    var $code;
    var $attendance = [];

    public static function createFromSrc($data) {
        $self = new self();
        $self->name = $data->{"School Name"};
        unset($data->{"School Name"});
        $self->code = $data->{"School Code"};
        unset($data->{"School Code"});

        foreach ($data as $srcYear => $number) {
            $year = (int)str_replace('HC_', '', $srcYear);
            if ($year < 1) {
                echo 'Invalid key - not a year `'.$srcYear.'`',"\n";
                continue;
            }
            $self->attendance[$year] = $number;
        }
        ksort($self->attendance);
        return $self;
    }
};

$collection = new \schoolCollection();
$collection->webSrc = 'https://data.cese.nsw.gov.au/data/dataset/1a8ee944-e56c-3480-aaf9-683047aa63a0/resource/64f0e82f-f678-4cec-9283-0b343aff1c61/download/headcount.json';

assert($collection->load() == $collection->getCount() > 0);
assert($collection->getByName('Albury Public School') instanceOf \school);
assert($collection->getByCode(1017) instanceOf \school);

echo 'There are '.$collection->getCount().' schools with attendance data in the collection'."\n";