<?php
/**
 * Tags data transformer.
 */

namespace App\Form\DataTransformer;

use App\Entity\TagBookmark;
use App\Repository\TagBookmarkRepository;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class TagsDataTransformer.
 */
class TagsBookmarksDataTransformer implements DataTransformerInterface
{
    /**
     * Tag repository.
     *
     * @var \App\Repository\TagRepository|null
     */
    private $repository = null;

    /**
     * TagsDataTransformer constructor.
     *
     * @param \App\Repository\TagBookmarkRepository $repository TagBookmark repository
     */
    public function __construct(TagBookmarkRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Transform array of tags to string of names.
     *
     * @param \Doctrine\Common\Collections\Collection $tags Tags entity collection
     *
     * @return string Result
     */
    public function transform($tags): string
    {
        if (null == $tags) {
            return '';
        }

        $tagNames = [];

        foreach ($tags as $tag) {
            $tagNames[] = $tag->getTitle();
        }

        return implode(',', $tagNames);
    }

    /**
     * Transform string of tag names into array of Tag entities.
     *
     * @param string $value String of tag names
     *
     * @return array Result
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function reverseTransform($value): array
    {
        $tagTitles = explode(',', $value);

        $tags = [];

        foreach ($tagTitles as $tagTitle) {
            if ('' !== trim($tagTitle)) {
                $tag = $this->repository->findOneByTitle(strtolower($tagTitle));
                if (null == $tag) {
                    $tag = new TagBookmark();
                    $tag->setTitle($tagTitle);
                    $this->repository->save($tag);
                }
                $tags[] = $tag;
            }
        }

        return $tags;
    }
}
