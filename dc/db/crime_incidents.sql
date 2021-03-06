-- 
-- Table structure for table `crime_incidents_2008`
-- 

CREATE TABLE crime_incidents_2008 (
  NID int(10) NOT NULL,
  CCN varchar(10) NOT NULL,
  REPORTDATETIME varchar(22) NOT NULL,
  SHIFT varchar(5) NOT NULL,
  OFFENSE varchar(50) NOT NULL,
  METHOD varchar(50) NOT NULL,
  LASTMODIFIEDDATE varchar(22) NOT NULL,
  BLOCKSITEADDRESS varchar(120) NOT NULL,
  NARRATIVE text NOT NULL,
  LATITUDE double NOT NULL,
  LONGITUDE double NOT NULL,
  CITY varchar(30) NOT NULL,
  STATE varchar(2) NOT NULL,
  WARD int(2) NOT NULL,
  ANC varchar(5) NOT NULL,
  SMD varchar(10) NOT NULL,
  DISTRICT varchar(15) NOT NULL,
  PSA int(3) NOT NULL,
  NEIGHBORHOODCLUSTER int(3) NOT NULL,
  HOTSPOT2006NAME varchar(75) NOT NULL,
  HOTSPOT2005NAME varchar(75) NOT NULL,
  HOTSPOT2004NAME varchar(75) NOT NULL,
  BUSINESSIMPROVEMENTDISTRICT varchar(75) NOT NULL,
  PRIMARY KEY  (NID),
  KEY LATITUDE (LATITUDE,LONGITUDE,WARD)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

