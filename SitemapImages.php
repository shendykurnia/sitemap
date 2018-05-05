<?php
namespace samdark\sitemap;

/**
 * A class for generating image Sitemaps (http://www.sitemaps.org/)
 *
 * @author SunwelLight <sunwellight@gmail.com>
 */
class SitemapImages extends Sitemap
{
    /**
     * Adds a new item to sitemap images
     *
     * @param string|array $location location item URL
     * @param array $images image array page
     *
     * @throws \InvalidArgumentException
     */
    public function addImage($location, $images, $lastModified = null, $changeFrequency = null, $priority = null)
    {
        if ($this->urlsCount >= $this->maxUrls) {
            $this->finishFile();
        }

        if ($this->writerBackend === null) {
            $this->useImage = TRUE;
            $this->createNewFile();
        }

        $this->addGroupingImage($location, $images, $lastModified, $changeFrequency, $priority);

        $this->urlsCount++;

        if ($this->urlsCount % $this->bufferSize === 0) {
            $this->flush();
        }
    }

    /**
     * Adds a new single item to sitemap images
     *
     * @param string $location location item URL
     * @param array $images image array page
     *
     * @throws \InvalidArgumentException
     *
     * @see addItem
     */
    private function addGroupingImage($location, $images, $lastModified, $changeFrequency, $priority)
    {
        $this->validateLocation($location);

        $this->writer->startElement('url');

        $this->writer->writeElement('loc', $location);

        if ($lastModified !== null) {
            $this->writer->writeElement('lastmod', date('c', $lastModified));
        }

        if ($changeFrequency !== null) {
            if (!in_array($changeFrequency, $this->validFrequencies, true)) {
                throw new \InvalidArgumentException(
                    'Please specify valid changeFrequency. Valid values are: '
                    . implode(', ', $this->validFrequencies)
                    . "You have specified: {$changeFrequency}."
                );
            }
            $this->writer->writeElement('changefreq', $changeFrequency);
        }

        if ($priority !== null) {
            if (!is_numeric($priority) || $priority < 0 || $priority > 1) {
                throw new \InvalidArgumentException(
                    "Please specify valid priority. Valid values range from 0.0 to 1.0. You have specified: {$priority}."
                );
            }
            $this->writer->writeElement('priority', number_format($priority, 1, '.', ','));
        }

        if(is_array($images)) {
            foreach ($images AS $image) {
                $this->writer->startElement('image:image');

                if(!empty($image['loc'])) {
                    $this->writer->writeElement('image:loc', $image['loc']);
                }
                if(!empty($image['caption'])) {
                    $this->writer->writeElement('image:caption', $image['caption']);
                }
                if(!empty($image['geo_location'])) {
                    $this->writer->writeElement('image:geo_location', $image['geo_location']);
                }
                if(!empty($image['title'])) {
                    $this->writer->writeElement('image:title', $image['title']);
                }
                if(!empty($image['license'])) {
                    $this->writer->writeElement('image:license', $image['license']);
                }

                $this->writer->endElement();
            }
        }

        $this->writer->endElement();
    }
}
