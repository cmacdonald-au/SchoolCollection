# School Collection

This is an interactive excercise - talk through your solution as you create it.

Given the following contract..

> ```$collection = new \schoolCollection();
> $collection->webSrc = 'https://data.cese.nsw.gov.au/data/dataset/1a8ee944-e56c-3480-aaf9-683047aa63a0/resource/64f0e82f-f678-4cec-9283-0b343aff1c61/download/headcount.json';
> 
> assert($collection->load() == $collection->getCount() > 0);
> assert($collection->getByName('Albury Public School') instanceOf \school);
> assert($collection->getByCode(1017) instanceOf \school);

Create a solution that fulfills the assertions.
