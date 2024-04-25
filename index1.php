<?php

$xmlString = '<VehAvail>
<VehAvailCore Status="Available">
<Vehicle AirConditionInd="true" TransmissionType="Automatic" FuelType="Unspecified" DriveType="Unspecified" PassengerQuantity="5" BaggageQuantity="3" Code="SCAR" CodeContext="CARTRAWLER">
<VehType VehicleCategory="1" DoorCount="2"/>
<VehClass Size="7"/>
<VehMakeModel Name="Volkswagen Jetta or similar" Code="SCAR"/>
<PictureURL>https://ctimg-fleet.cartrawler.com/volkswagen/jetta/primary.png</PictureURL>
<VehIdentity VehicleAssetNumber="26537"/>
</Vehicle>
<RentalRate>
<VehicleCharges>
<VehicleCharge Description="Unlimited mileage" TaxInclusive="true" IncludedInRate="true" Purpose="609.VCP.X"/>
</VehicleCharges>
<RateQualifier RateQualifier="PREPAID-IN" PromotionCode="INCLUSIVE_NO_EXCESS"/>
</RentalRate>
<TotalCharge RateTotalAmount="127.85" EstimatedTotalAmount="127.85" CurrencyCode="USD"/>
<PricedEquips>
<PricedEquip>
<Equipment EquipType="13">
<Description>GPS</Description>
</Equipment>
<Charge Amount="16.99" CurrencyCode="USD" TaxInclusive="false" IncludedInRate="false"/>
</PricedEquip>
</PricedEquips>
</VehAvailCore>
<VehAvailInfo>
<PricedCoverages>
<PricedCoverage>
<Coverage CoverageType="601.VCT.X"/>
<Charge Description="Extra insurance" TaxInclusive="true" IncludedInRate="true"/>
</PricedCoverage>
</PricedCoverages>
</VehAvailInfo>
</VehAvail>';


$xml = simplexml_load_string($xmlString);


function xmlToArrayWithAttributes($xml) {
    $array = [];

    
    foreach ($xml->attributes() as $key => $value) {
        $array['@attributes'][$key] = (string) $value;
    }


    foreach ($xml->children() as $child) {
        $key = $child->getName();
        $value = (string) $child;

        if ($child->count() > 0) {
            $array[$key][] = xmlToArrayWithAttributes($child);
        } else {
            $array[$key] = $value;
        }
    }

    return $array;
}


$arrayData = xmlToArrayWithAttributes($xml);


$jsonData = json_encode($arrayData);


echo "<h2>JSON Data:</h2>";
echo "<pre>" . htmlspecialchars($jsonData) . "</pre>";



$dbHost = 'localhost';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'xmlparser';


$conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$jsonToStore = $conn->real_escape_string($jsonData); 
$sql = "INSERT INTO xml_table (json_data) VALUES ('$jsonToStore')";
if ($conn->query($sql) === TRUE) {
    echo "JSON data stored successfully in the database.";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();

?>
