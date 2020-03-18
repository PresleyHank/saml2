<?php

declare(strict_types=1);

namespace SAML2\XML\samlp;

use DOMElement;
use Exception;
use RobRichards\XMLSecLibs\XMLSecEnc;
use RobRichards\XMLSecLibs\XMLSecurityKey;
use SAML2\DOMDocumentFactory;
use SAML2\Constants;
use SAML2\Utils;
use SAML2\XML\IdentifierTrait;
use SAML2\XML\ds\Signature;
use SAML2\XML\saml\IdentifierInterface;
use SAML2\XML\saml\Issuer;
use Webmozart\Assert\Assert;

/**
 * Class for SAML 2 logout request messages.
 *
 * @package SimpleSAMLphp
 */
class LogoutRequest extends AbstractRequest
{
    use IdentifierTrait;

    /**
     * The expiration time of this request.
     *
     * @var int|null
     */
    private $notOnOrAfter = null;

    /**
     * The SessionIndexes of the sessions that should be terminated.
     *
     * @var \SAML2\XML\samlp\SessionIndex[]
     */
    private $sessionIndexes = [];

    /**
     * The optional reason for the logout, typically a URN
     * See \SAML2\Constants::LOGOUT_REASON_*
     * From the standard section 3.7.3: "other values MAY be agreed on between participants"
     *
     * @var string|null
     */
    protected $reason = null;


    /**
     * Constructor for SAML 2 AttributeQuery.
     *
     * @param \SAML2\XML\saml\IdentifierInterface $identifier
     * @param int|null $notOnOrAfter
     * @param string|null $reason
     * @param \SAML2\XML\samlp\SessionIndex[]|null $sessionIndexes
     * @param \SAML2\XML\saml\Issuer|null $issuer
     * @param string|null $id
     * @param string|null $version
     * @param int|null $issueInstant
     * @param string|null $destination
     * @param string|null $consent
     * @param \SAML2\XML\samlp\Extensions $extensions
     */
    public function __construct(
        IdentifierInterface $identifier,
        int $notOnOrAfter = null,
        string $reason = null,
        array $sessionIndexes = [],
        ?Issuer $issuer = null,
        ?string $id = null,
        ?string $version = null,
        ?int $issueInstant = null,
        ?string $destination = null,
        ?string $consent = null,
        ?Extensions $extensions = null
    ) {
        parent::__construct($issuer, $id, $version, $issueInstant, $destination, $consent, $extensions);

        $this->setIdentifier($identifier);
        $this->setNotOnOrAfter($notOnOrAfter);
        $this->setReason($reason);
        $this->setSessionIndexes($sessionIndexes);
    }


    /**
     * Retrieve the expiration time of this request.
     *
     * @return int|null The expiration time of this request.
     */
    public function getNotOnOrAfter(): ?int
    {
        return $this->notOnOrAfter;
    }


    /**
     * Set the expiration time of this request.
     *
     * @param int|null $notOnOrAfter The expiration time of this request.
     * @return void
     */
    public function setNotOnOrAfter(int $notOnOrAfter = null): void
    {
        $this->notOnOrAfter = $notOnOrAfter;
    }

    /**
     * Retrieve the reason for this request.
     *
     * @return string|null The reason for this request.
     */
    public function getReason(): ?string
    {
        return $this->reason;
    }


    /**
     * Set the reason for this request.
     *
     * @param string|null $reason The optional reason for this request in URN format
     * @return void
     */
    public function setReason($reason = null): void
    {
        $this->reason = $reason;
    }


    /**
     * Retrieve the SessionIndexes of the sessions that should be terminated.
     *
     * @return \SAML2\XML\samlp\SessionIndex[] The SessionIndexes, or an empty array if all sessions should be terminated.
     */
    public function getSessionIndexes(): array
    {
        return $this->sessionIndexes;
    }


    /**
     * Set the SessionIndexes of the sessions that should be terminated.
     *
     * @param \SAML2\XML\samlp\SessionIndex[] $sessionIndexes The SessionIndexes, or an empty array if all sessions should be terminated.
     * @return void
     */
    public function setSessionIndexes(array $sessionIndexes): void
    {
        $this->sessionIndexes = $sessionIndexes;
    }


    /**
     * Convert XML into a LogoutRequest
     *
     * @param \DOMElement $xml The XML element we should load
     * @return \SAML2\XML\samlp\LogoutRequest
     * @throws \InvalidArgumentException if the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): object
    {
        Assert::same($xml->localName, 'LogoutRequest');
        Assert::same($xml->namespaceURI, LogoutRequest::NS);

        $id = self::getAttribute($xml, 'ID');
        $version = self::getAttribute($xml, 'Version');
        $issueInstant = Utils::xsDateTimeToTimestamp(self::getAttribute($xml, 'IssueInstant'));
        $inResponseTo = self::getAttribute($xml, 'InResponseTo', null);
        $destination = self::getAttribute($xml, 'Destination', null);
        $consent = self::getAttribute($xml, 'Consent', null);

        $notOnOrAfter = self::getAttribute($xml, 'NotOnOrAfter', null);
        if ($notOnOrAfter !== null) {
            $notOnOrAfter = Utils::xsDateTimeToTimestamp($notOnOrAfter);
        }

        $reason = self::getAttribute($xml, 'Reason', null);
        $sessionIndexes = SessionIndex::getChildrenOfClass($xml);

        $issuer = Issuer::getChildrenOfClass($xml);
        Assert::countBetween($issuer, 0, 1);

        $extensions = Extensions::getChildrenOfClass($xml);
        Assert::maxCount($extensions, 1, 'Only one saml:Extensions element is allowed.');

        $identifier = self::getIdentifierFromXML($xml);
        Assert::notNull($identifier, 'Missing <saml:NameID>, <saml:BaseID> or <saml:EncryptedID> in <samlp:LogoutRequest>.');

        $signature = Signature::getChildrenOfClass($xml);
        Assert::maxCount($signature, 1, 'Only one ds:Signature element is allowed.');

        $request = new self(
            $identifier,
            $notOnOrAfter,
            $reason,
            $sessionIndexes,
            array_pop($issuer),
            $id,
            $version,
            $issueInstant,
            $destination,
            $consent,
            array_pop($extensions)
        );

        if (!empty($signature)) {
            $request->setSignature($signature[0]);
        }

        return $request;
    }


    /**
     * Convert this logout request message to an XML element.
     *
     * @return \DOMElement This logout request.
     */
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        Assert::null($parent);

        $root = parent::toXML($parent);

        if ($this->notOnOrAfter !== null) {
            $root->setAttribute('NotOnOrAfter', gmdate('Y-m-d\TH:i:s\Z', $this->notOnOrAfter));
        }

        if ($this->reason !== null) {
            $root->setAttribute('Reason', $this->reason);
        }

        $this->identifier->toXML($root);

        foreach ($this->sessionIndexes as $sessionIndex) {
            $sessionIndex->toXML($root);
        }

        return $this->signElement($root);
    }
}
