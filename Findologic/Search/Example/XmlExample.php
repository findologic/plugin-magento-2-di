<?php

namespace Findologic\Search\Example;

use DateTime;
use FINDOLOGIC\Export\Data\Attribute;
use FINDOLOGIC\Export\Data\Image;
use FINDOLOGIC\Export\Data\Item;
use FINDOLOGIC\Export\Data\Keyword;
use FINDOLOGIC\Export\Data\Ordernumber;
use FINDOLOGIC\Export\Data\Property;
use FINDOLOGIC\Export\Data\Usergroup;
use FINDOLOGIC\Export\Exporter;

/**
 * This example class builds an XML export based on the example of the FINDOLOGIC documentation, which can be found
 * under the following link:
 * @link https://docs.findologic.com/doku.php?id=xml_export_documentation:XML_format
 *
 * The XML export is built with the libflexport library, which can be found under the following link:
 * @link https://github.com/findologic/libflexport
 */
class XmlExample
{
    /**
     * @var Item
     */
    private $item;

    public function createExport()
    {
        $items = [];
        $exporter = Exporter::create(Exporter::TYPE_XML);

        $products[] = new ExampleProductItem();
        $products[] = new ExampleContentItem();

        foreach ($products as $product) {
            $this->item = $exporter->createItem($product->id);

            $this->addOrdernumbers($product);
            $this->addNames($product);
            $this->addSummaries($product);
            $this->addDescriptions($product);
            $this->addPrices($product);
            $this->addUrls($product);
            $this->addKeywords($product);
            $this->addBonuses($product);
            $this->addSalesFrequencies($product);
            $this->addDateAddeds($product);
            $this->addSorts($product);
            $this->addUserGroups($product);
            $this->addImages($product);
            $this->addAttributes($product);
            $this->addProperties($product);

            $items[] = $this->item;
        }

        return $exporter->serializeItems($items, 0, 2, 2);
    }

    private function addOrdernumbers(ExampleBaseItem $product)
    {
        foreach ($product->ordernumbers as $userGroup => $ordernumbers) {
            foreach ($ordernumbers as $ordernumber) {
                $this->item->addOrdernumber(new Ordernumber($ordernumber, $userGroup));
            }
        }
    }

    private function addNames(ExampleBaseItem $product)
    {
        foreach ($product->names as $userGroup => $name) {
            $this->item->addName($name, $userGroup);
        }
    }

    private function addSummaries(ExampleBaseItem $product)
    {
        foreach ($product->summaries as $userGroup => $summary) {
            $this->item->addSummary($summary, $userGroup);
        }
    }

    private function addDescriptions(ExampleBaseItem $product)
    {
        foreach ($product->descriptions as $userGroup => $description) {
            $this->item->addDescription($description, $userGroup);
        }
    }

    private function addPrices(ExampleBaseItem $product)
    {
        foreach ($product->prices as $userGroup => $price) {
            $this->item->addPrice($price, $userGroup);
        }
    }

    private function addUrls(ExampleBaseItem $product)
    {
        foreach ($product->urls as $userGroup => $url) {
            $this->item->addUrl($url, $userGroup);
        }
    }

    private function addKeywords(ExampleBaseItem $product)
    {
        foreach ($product->keywords as $userGroup => $keywords) {
            foreach ($keywords as $keyword) {
                $this->item->addKeyword(new Keyword($keyword, $userGroup));
            }
        }
    }

    private function addBonuses(ExampleBaseItem $product)
    {
        foreach ($product->bonuses as $userGroup => $bonus) {
            $this->item->addBonus($bonus, $userGroup);
        }
    }

    private function addSalesFrequencies(ExampleBaseItem $product)
    {
        foreach ($product->salesFrequencies as $userGroup => $salesFrequency) {
            $this->item->addSalesFrequency($salesFrequency, $userGroup);
        }
    }

    private function addDateAddeds(ExampleBaseItem $product)
    {
        foreach ($product->dateAddeds as $userGroup => $dateAdded) {
            $this->item->addDateAdded(new DateTime($dateAdded), $userGroup);
        }
    }

    private function addSorts(ExampleBaseItem $product)
    {
        foreach ($product->sorts as $userGroup => $sort) {
            $this->item->addSort($sort, $userGroup);
        }
    }

    private function addUserGroups(ExampleBaseItem $product)
    {
        foreach ($product->userGroups as $userGroup) {
            $this->item->addUsergroup(new Usergroup($userGroup));
        }
    }

    private function addImages(ExampleBaseItem $product)
    {
        foreach ($product->images as $userGroup => $images) {
            foreach ($images as $image => $type) {
                $this->item->addImage(new Image($image, $type, $userGroup));
            }
        }
    }

    private function addAttributes(ExampleBaseItem $product)
    {
        foreach ($product->attributes as $attributeName => $attributeValues) {
            $this->item->addAttribute(new Attribute($attributeName, $attributeValues));
        }
    }

    private function addProperties(ExampleBaseItem $product)
    {
        foreach ($product->properties as $propertyName => $values) {
            if ($propertyName === 'variants') {
                foreach ($values as $userGroup => $value) {
                    $values[$userGroup] = json_encode($value);
                }
            }

            $propertyElement = new Property($propertyName, $values);
            $this->item->addProperty($propertyElement);
        }
    }
}
