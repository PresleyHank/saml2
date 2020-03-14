<?php

declare(strict_types=1);

namespace SAML2\XML\samlp;

use PHPUnit\Framework\TestCase;
use SAML2\XML\saml\Issuer;
use SAML2\DOMDocumentFactory;
use SAML2\Utils;

class ArtifactResolveTest extends TestCase
{
    /**
     * @return void
     */
    public function testMarshalling(): void
    {
        $issuer = new Issuer('urn:example:issuer');
        $artifact = 'AAQAADWNEw5VT47wcO4zX/iEzMmFQvGknDfws2ZtqSGdkNSbsW1cmVR0bzU=';

        $artifactResolve = new ArtifactResolve($artifact, $issuer);

        $artifactResolveElement = $artifactResolve->toXML();
        $artelement = Utils::xpQuery($artifactResolveElement, './saml_protocol:Artifact');

        $this->assertCount(1, $artelement);
        $this->assertEquals($artifact, $artelement[0]->textContent);
    }


    /**
     * @return void
     */
    public function testUnmarshalling(): void
    {
        $id = '_6c3a4f8b9c2d';
        $artifact = 'AAQAADWNEw5VT47wcO4zX/iEzMmFQvGknDfws2ZtqSGdkNSbsW1cmVR0bzU=';

        $issuer = new Issuer('https://ServiceProvider.com/SAML');

        $xml = <<<XML
<samlp:ArtifactResolve
	xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"
	xmlns="urn:oasis:names:tc:SAML:2.0:assertion"
	ID="{$id}" Version="2.0"
	IssueInstant="2004-01-21T19:00:49Z">
	<Issuer>{$issuer}</Issuer>
	<samlp:Artifact>{$artifact}</samlp:Artifact>
</samlp:ArtifactResolve>
XML;
        $document = DOMDocumentFactory::fromString($xml);
        $ar = ArtifactResolve::fromXML($document->firstChild);

        $this->assertEquals($artifact, $ar->getArtifact());
        $this->assertEquals($id, $ar->getId());
        $this->assertEquals($issuer->getValue(), $ar->getIssuer()->getValue());
    }
}
