<?php
/**
 * Bookmark type.
 */

namespace App\Form;

use App\Entity\Bookmark;
use App\Form\DataTransformer\TagsBookmarksDataTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BookmarkType.
 */
class BookmarkType extends AbstractType
{
    /**
     * TagsBookmarks data transformer.
     *
     * @var \App\Form\DataTransformer\TagsBookmarksDataTransformer|null
     */
    private $tagsDataTransformer = null;

    /**
     * BookmarkType constructor.
     *
     * @param \App\Form\DataTransformer\TagsBookmarksDataTransformer $tagsDataTransformer TagsBookmarks data transformer
     */
    public function __construct(TagsBookmarksDataTransformer $tagsDataTransformer)
    {
        $this->tagsDataTransformer = $tagsDataTransformer;
    }

    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting from the
     * top most type. Type extensions can further modify the form.
     *
     * @see FormTypeExtensionInterface::buildForm()
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'title',
            TextType::class,
            [
                'label' => 'label.title',
                'required' => true,
                'attr' => ['max_length' => 45],
            ]
        );
        $builder->add(
            'url',
            TextType::class,
            [
                'label' => 'label.url',
                'required' => true,
                'attr' => ['max_length' => 255],
            ]
        );
        $builder->add(
            'tags',
            TextType::class,
            [
                'label' => 'label.tags',
                'required' => false,
                'attr' => [
                    'max_length' => 255,
                ],
            ]
        );
        $builder->get('tags')->addModelTransformer(
            $this->tagsDataTransformer
        );
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Bookmark::class]);
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * The block prefix defaults to the underscored short class name with
     * the "Type" suffix removed (e.g. "UserProfileType" => "user_profile").
     *
     * @return string The prefix of the template block name
     */
    public function getBlockPrefix(): string
    {
        return 'bookmark';
    }
}
