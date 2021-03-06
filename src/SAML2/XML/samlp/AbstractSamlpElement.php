<?php

declare(strict_types=1);

namespace SAML2\XML\samlp;

use SAML2\Constants;
use SAML2\XML\AbstractXMLElement;

/**
 * Abstract class to be implemented by all the classes in this namespace
 *
 * @author Tim van Dijen, <tvdijen@gmail.com>
 * @package simplesamlphp/saml2
 */
abstract class AbstractSamlpElement extends AbstractXMLElement
{
    /** @var string */
    public const NS = Constants::NS_SAMLP;

    /** @var string */
    public const NS_PREFIX = 'samlp';
}
