<?php declare(strict_types=1);

namespace MainhattanWheels\Storefront\Controller;

use Shopware\Core\Content\ContactForm\SalesChannel\AbstractContactFormRoute;
use Shopware\Core\Content\Newsletter\SalesChannel\AbstractNewsletterSubscribeRoute;
use Shopware\Core\Content\Newsletter\SalesChannel\AbstractNewsletterUnsubscribeRoute;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\Framework\Validation\Exception\ConstraintViolationException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\FormController;
use Shopware\Storefront\Framework\Routing\RequestTransformer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route(defaults: ['_routeScope' => ['storefront']])]

class NewsletterFormController extends FormController{
    /**
     * @var AbstractNewsletterSubscribeRoute
     */
    protected AbstractNewsletterSubscribeRoute $subscribeRoute;

    /**
     * @var AbstractNewsletterUnsubscribeRoute
     */
    protected AbstractNewsletterUnsubscribeRoute $unsubscribeRoute;

    public function __construct(
        AbstractContactFormRoute           $contactFormRoute,
        AbstractNewsletterSubscribeRoute   $subscribeRoute,
        AbstractNewsletterUnsubscribeRoute $unsubscribeRoute
    )
    {
        parent::__construct($contactFormRoute,
            $this->subscribeRoute = $subscribeRoute,
            $this->unsubscribeRoute = $unsubscribeRoute
        );
    }

    #[Route(path: '/form/newsletter/custom/', name: 'frontend.form.newsletter.register.handle.custom', defaults: ['XmlHttpRequest' => true, '_captcha' => false], methods: ['POST'])]
    public function handleNewsletter(Request $request, RequestDataBag $data, SalesChannelContext $context): JsonResponse
    {
        $subscribe = $data->get('option') === self::SUBSCRIBE;
        if ($subscribe) {
            $response = $this->handleSubscribe($request, $data, $context);
        } else {
            $response = $this->handleUnsubscribe($data, $context);
        }

        return new JsonResponse($response);
    }

    protected function handleSubscribe(Request $request, RequestDataBag $data, SalesChannelContext $context): array
    {

        try {
            $data->set('storefrontUrl', $request->attributes->get(RequestTransformer::STOREFRONT_URL));
            $this->subscribeRoute->subscribe($data, $context, false);
            $response[] = [
                'type' => 'success',
                'alert' => $this->trans('newsletter.subscriptionPersistedSuccess'),
            ];
            $response[] = [
                'type' => 'info',
                'alert' => $this->renderView('@Storefront/storefront/utilities/alert.html.twig', [
                    'type' => 'info',
                    'list' => [$this->trans('newsletter.subscriptionPersistedInfo')],
                ]),
            ];
        } catch (ConstraintViolationException $exception) {
            $errors = [];
            foreach ($exception->getViolations() as $error) {
                $errors[] = $error->getMessage();
            }
            $response[] = [
                'type' => 'danger',
                'alert' => $this->renderView('@Storefront/storefront/utilities/alert.html.twig', [
                    'type' => 'danger',
                    'list' => $errors,
                ]),
            ];
        } catch (\Exception $exception) {
            $response[] = [
                'type' => 'danger',
                'alert' => $this->renderView('@Storefront/storefront/utilities/alert.html.twig', [
                    'type' => 'danger',
                    'list' => [$this->trans('error.message-default')],
                ]),
            ];
        }

        return $response;
    }

    protected function handleUnsubscribe(RequestDataBag $data, SalesChannelContext $context): array
    {
        try {
            $this->unsubscribeRoute->unsubscribe($data, $context);
            $response[] = [
                'type' => 'success',
                'alert' => $this->trans('newsletter.subscriptionRevokeSuccess'),
            ];
        } catch (ConstraintViolationException $exception) {
            $errors = [];
            foreach ($exception->getViolations() as $error) {
                $errors[] = $error->getMessage();
            }
            $response[] = [
                'type' => 'danger',
                'alert' => $this->renderView('@Storefront/storefront/utilities/alert.html.twig', [
                    'type' => 'danger',
                    'list' => $errors,
                ]),
            ];
        } catch (\Exception $exception) {
            $response = [];
        }

        return $response;
    }
}