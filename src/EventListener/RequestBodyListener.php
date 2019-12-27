<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\AcceptHeader;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Serializer\SerializerInterface;

class RequestBodyListener implements EventSubscriberInterface
{
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        if (!$request->attributes->get(ZoneMatcherListener::ZONE_ATTRIBUTE, true)) {
            return;
        }

        $contentType = $request->getContentType();
        if (null === $contentType || false === $this->serializer->supportsDecoding($contentType)) {
            return;
        }

        $data = $this->serializer->decode($request->getContent(), $contentType);
        $request->request = new ParameterBag($data);

        /**
         * 默认情问下，响应格式将与请求参数格式相同，如果需要指定响应格式，
         * 需要在请求中添加 Accept 头，比如：Accept: application/json;q=3, application/xml;q=5, text/plain;q=10
         * 则按 Accept 规范中的优先级，将返回 application/xml 格式
         * 
         * @see https://developer.mozilla.org/zh-CN/docs/Web/HTTP/Headers/Accept
         * @see https://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
         */
        // $preferredType = $contentType;
        // foreach ($request->getAcceptableContentTypes() as $mimeType) {
        //     $format = $request->getFormat($mimeType);
        //     if ($format && false !== $this->serializer->supportsDecoding($format)) {
        //         $preferredType = $format;
        //         break;
        //     }
        // }
        // dd($preferredType);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 16],
        ];
    }
}
