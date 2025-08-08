-- --------------------------------------------------------
-- Host:                         192.168.10.150
-- Versione server:              Microsoft SQL Server 2016 (SP3-GDR) (KB5046855) - 13.0.6455.2
-- S.O. server:                  Windows Server 2019 Datacenter 10.0 <X64> (Build 17763: ) (Hypervisor)
-- HeidiSQL Versione:            12.11.0.7065
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES  */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dump della struttura del database RomaPass2018
CREATE DATABASE IF NOT EXISTS "RomaPass2018";
USE "RomaPass2018";

-- Dump della struttura di tabella RomaPass2018.cercaCodici
CREATE TABLE IF NOT EXISTS "cercaCodici" (
	"ID" INT,
	"testo" VARCHAR(4096) COLLATE Latin1_General_100_BIN2,
	FOREIGN KEY INDEX "FK_cercaCodici_tblCodici" ("ID"),
	PRIMARY KEY ("ID"),
	CONSTRAINT "FK_cercaCodici_tblCodici" FOREIGN KEY ("ID") REFERENCES "tblCodici" ("ID") ON UPDATE CASCADE ON DELETE CASCADE
);

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella RomaPass2018.cercaOrdini
CREATE TABLE IF NOT EXISTS "cercaOrdini" (
	"ID" INT,
	"testo" VARCHAR(4096) COLLATE Latin1_General_100_BIN2,
	PRIMARY KEY ("ID"),
	FOREIGN KEY INDEX "FK_cercaOrdini_tblOrdini" ("ID"),
	CONSTRAINT "FK_cercaOrdini_tblOrdini" FOREIGN KEY ("ID") REFERENCES "tblOrdini" ("ID") ON UPDATE CASCADE ON DELETE CASCADE
);

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di funzione RomaPass2018.fn_diagramobjects
DELIMITER //

	CREATE FUNCTION dbo.fn_diagramobjects() 
	RETURNS int
	WITH EXECUTE AS N'dbo'
	AS
	BEGIN
		declare @id_upgraddiagrams		int
		declare @id_sysdiagrams			int
		declare @id_helpdiagrams		int
		declare @id_helpdiagramdefinition	int
		declare @id_creatediagram	int
		declare @id_renamediagram	int
		declare @id_alterdiagram 	int 
		declare @id_dropdiagram		int
		declare @InstalledObjects	int

		select @InstalledObjects = 0

		select 	@id_upgraddiagrams = object_id(N'dbo.sp_upgraddiagrams'),
			@id_sysdiagrams = object_id(N'dbo.sysdiagrams'),
			@id_helpdiagrams = object_id(N'dbo.sp_helpdiagrams'),
			@id_helpdiagramdefinition = object_id(N'dbo.sp_helpdiagramdefinition'),
			@id_creatediagram = object_id(N'dbo.sp_creatediagram'),
			@id_renamediagram = object_id(N'dbo.sp_renamediagram'),
			@id_alterdiagram = object_id(N'dbo.sp_alterdiagram'), 
			@id_dropdiagram = object_id(N'dbo.sp_dropdiagram')

		if @id_upgraddiagrams is not null
			select @InstalledObjects = @InstalledObjects + 1
		if @id_sysdiagrams is not null
			select @InstalledObjects = @InstalledObjects + 2
		if @id_helpdiagrams is not null
			select @InstalledObjects = @InstalledObjects + 4
		if @id_helpdiagramdefinition is not null
			select @InstalledObjects = @InstalledObjects + 8
		if @id_creatediagram is not null
			select @InstalledObjects = @InstalledObjects + 16
		if @id_renamediagram is not null
			select @InstalledObjects = @InstalledObjects + 32
		if @id_alterdiagram  is not null
			select @InstalledObjects = @InstalledObjects + 64
		if @id_dropdiagram is not null
			select @InstalledObjects = @InstalledObjects + 128
		
		return @InstalledObjects 
	END
	//
DELIMITER ;

-- Dump della struttura di funzione RomaPass2018.pbfn_getCodiceRitiro
DELIMITER //
CREATE FUNCTION [dbo].[pbfn_getCodiceRitiro]() RETURNS char(9)
AS
begin
declare @chars varchar(50)
declare @l int, @mx int, @i int, @j int, @nx int, @off int
declare @ret varchar(9)
set @nx=9 -- numero di caratteri del codice
set @mx=100 -- numero max di tentativi di trovare codice univoco
set @chars='23456789ABCDEFGHJKLMNPQRSTUVWXYZ'
set @l=len(@chars)
set @i=0
while @i<@mx
begin
	set @ret=''
	set @j=0
	while @j<@nx
	begin
		set @off=1+floor((select randNumber from pbv_getRandNumber)*@l)
		set @ret=@ret+substring(@chars,@off,1)
		set @j=@j+1
	end
	if not exists (select * from tblOrdini where codiceRitiro=@ret)
		break
	set @i=@i+1
end
return cast(@ret as char(9))
end

//
DELIMITER ;

-- Dump della struttura di funzione RomaPass2018.pbfn_getCodiceRitiro2
DELIMITER //
CREATE FUNCTION [dbo].[pbfn_getCodiceRitiro2](@firstChar char(1)) RETURNS varchar(11)
AS
begin
	declare @chars varchar(50)
	declare @l int, @mx int, @i int, @j int, @nx int, @off int, @sum int
	declare @ret varchar(11)
	set @nx=9 -- numero di caratteri del codice
	set @mx=100 -- numero max di tentativi di trovare codice univoco
	set @chars='23456789ABCDEFGHJKLMNPQRSTUVWXYZ'
	set @l=len(@chars)
	set @i=0
	while @i<@mx
	begin
		set @ret=@firstChar
		set @sum=CHARINDEX(@firstChar,@chars)
		set @j=1
		while @j<=@nx
		begin
			set @off=1+floor((select randNumber from pbv_getRandNumber)*@l)
			set @ret=@ret+substring(@chars,@off,1)
			set @j=@j+1
			set @sum = @sum + @j * @off
		end
		-- aggiungo carattere di controllo
		set @off=1 + (@sum % @l)
		set @ret=@ret+substring(@chars,@off,1)
		if not exists (select top 1 1 from tblCodici where codiceRitiro=@ret) AND  not exists (select top 1 1 from tblOrdini where codiceRitiro=@ret)
			break
		set @i=@i+1
	end
	return @ret
	--return cast(@ret as char(11))
end

//
DELIMITER ;

-- Dump della struttura di funzione RomaPass2018.pbfn_getPwd
DELIMITER //
CREATE FUNCTION [dbo].[pbfn_getPwd]() RETURNS varchar(10)
AS
begin
declare @chars varchar(70)
declare @l int, @j int, @nx int, @off int
declare @ret varchar(10)
set @nx=10 -- numero di caratteri della pwd
set @chars='23456789ABCDEFGHJKLMNPQRSTUVWXYZ'
set @l=len(@chars)
set @ret=''
set @j=0
while @j<@nx
begin
	set @off=1+floor((select randNumber from pbv_getRandNumber)*@l)
	set @ret=@ret+substring(@chars,@off,1)
	set @j=@j+1
end
return @ret
end

//
DELIMITER ;

-- Dump della struttura di vista RomaPass2018.pbv_getRandNumber
-- Creazione di una tabella temporanea per risolvere gli errori di dipendenza della vista
CREATE TABLE "pbv_getRandNumber" (
	"RandNumber" FLOAT
);

-- Dump della struttura di procedura RomaPass2018.sp_alterdiagram
DELIMITER //

	CREATE PROCEDURE dbo.sp_alterdiagram
	(
		@diagramname 	sysname,
		@owner_id	int	= null,
		@version 	int,
		@definition 	varbinary(max)
	)
	WITH EXECUTE AS 'dbo'
	AS
	BEGIN
		set nocount on
	
		declare @theId 			int
		declare @retval 		int
		declare @IsDbo 			int
		
		declare @UIDFound 		int
		declare @DiagId			int
		declare @ShouldChangeUID	int
	
		if(@diagramname is null)
		begin
			RAISERROR ('Invalid ARG', 16, 1)
			return -1
		end
	
		execute as caller;
		select @theId = DATABASE_PRINCIPAL_ID();	 
		select @IsDbo = IS_MEMBER(N'db_owner'); 
		if(@owner_id is null)
			select @owner_id = @theId;
		revert;
	
		select @ShouldChangeUID = 0
		select @DiagId = diagram_id, @UIDFound = principal_id from dbo.sysdiagrams where principal_id = @owner_id and name = @diagramname 
		
		if(@DiagId IS NULL or (@IsDbo = 0 and @theId <> @UIDFound))
		begin
			RAISERROR ('Diagram does not exist or you do not have permission.', 16, 1);
			return -3
		end
	
		if(@IsDbo <> 0)
		begin
			if(@UIDFound is null or USER_NAME(@UIDFound) is null) -- invalid principal_id
			begin
				select @ShouldChangeUID = 1 ;
			end
		end

		-- update dds data			
		update dbo.sysdiagrams set definition = @definition where diagram_id = @DiagId ;

		-- change owner
		if(@ShouldChangeUID = 1)
			update dbo.sysdiagrams set principal_id = @theId where diagram_id = @DiagId ;

		-- update dds version
		if(@version is not null)
			update dbo.sysdiagrams set version = @version where diagram_id = @DiagId ;

		return 0
	END
	//
DELIMITER ;

-- Dump della struttura di procedura RomaPass2018.sp_creatediagram
DELIMITER //

	CREATE PROCEDURE dbo.sp_creatediagram
	(
		@diagramname 	sysname,
		@owner_id		int	= null, 	
		@version 		int,
		@definition 	varbinary(max)
	)
	WITH EXECUTE AS 'dbo'
	AS
	BEGIN
		set nocount on
	
		declare @theId int
		declare @retval int
		declare @IsDbo	int
		declare @userName sysname
		if(@version is null or @diagramname is null)
		begin
			RAISERROR (N'E_INVALIDARG', 16, 1);
			return -1
		end
	
		execute as caller;
		select @theId = DATABASE_PRINCIPAL_ID(); 
		select @IsDbo = IS_MEMBER(N'db_owner');
		revert; 
		
		if @owner_id is null
		begin
			select @owner_id = @theId;
		end
		else
		begin
			if @theId <> @owner_id
			begin
				if @IsDbo = 0
				begin
					RAISERROR (N'E_INVALIDARG', 16, 1);
					return -1
				end
				select @theId = @owner_id
			end
		end
		-- next 2 line only for test, will be removed after define name unique
		if EXISTS(select diagram_id from dbo.sysdiagrams where principal_id = @theId and name = @diagramname)
		begin
			RAISERROR ('The name is already used.', 16, 1);
			return -2
		end
	
		insert into dbo.sysdiagrams(name, principal_id , version, definition)
				VALUES(@diagramname, @theId, @version, @definition) ;
		
		select @retval = @@IDENTITY 
		return @retval
	END
	//
DELIMITER ;

-- Dump della struttura di procedura RomaPass2018.sp_dropdiagram
DELIMITER //

	CREATE PROCEDURE dbo.sp_dropdiagram
	(
		@diagramname 	sysname,
		@owner_id	int	= null
	)
	WITH EXECUTE AS 'dbo'
	AS
	BEGIN
		set nocount on
		declare @theId 			int
		declare @IsDbo 			int
		
		declare @UIDFound 		int
		declare @DiagId			int
	
		if(@diagramname is null)
		begin
			RAISERROR ('Invalid value', 16, 1);
			return -1
		end
	
		EXECUTE AS CALLER;
		select @theId = DATABASE_PRINCIPAL_ID();
		select @IsDbo = IS_MEMBER(N'db_owner'); 
		if(@owner_id is null)
			select @owner_id = @theId;
		REVERT; 
		
		select @DiagId = diagram_id, @UIDFound = principal_id from dbo.sysdiagrams where principal_id = @owner_id and name = @diagramname 
		if(@DiagId IS NULL or (@IsDbo = 0 and @UIDFound <> @theId))
		begin
			RAISERROR ('Diagram does not exist or you do not have permission.', 16, 1)
			return -3
		end
	
		delete from dbo.sysdiagrams where diagram_id = @DiagId;
	
		return 0;
	END
	//
DELIMITER ;

-- Dump della struttura di procedura RomaPass2018.sp_helpdiagramdefinition
DELIMITER //

	CREATE PROCEDURE dbo.sp_helpdiagramdefinition
	(
		@diagramname 	sysname,
		@owner_id	int	= null 		
	)
	WITH EXECUTE AS N'dbo'
	AS
	BEGIN
		set nocount on

		declare @theId 		int
		declare @IsDbo 		int
		declare @DiagId		int
		declare @UIDFound	int
	
		if(@diagramname is null)
		begin
			RAISERROR (N'E_INVALIDARG', 16, 1);
			return -1
		end
	
		execute as caller;
		select @theId = DATABASE_PRINCIPAL_ID();
		select @IsDbo = IS_MEMBER(N'db_owner');
		if(@owner_id is null)
			select @owner_id = @theId;
		revert; 
	
		select @DiagId = diagram_id, @UIDFound = principal_id from dbo.sysdiagrams where principal_id = @owner_id and name = @diagramname;
		if(@DiagId IS NULL or (@IsDbo = 0 and @UIDFound <> @theId ))
		begin
			RAISERROR ('Diagram does not exist or you do not have permission.', 16, 1);
			return -3
		end

		select version, definition FROM dbo.sysdiagrams where diagram_id = @DiagId ; 
		return 0
	END
	//
DELIMITER ;

-- Dump della struttura di procedura RomaPass2018.sp_helpdiagrams
DELIMITER //

	CREATE PROCEDURE dbo.sp_helpdiagrams
	(
		@diagramname sysname = NULL,
		@owner_id int = NULL
	)
	WITH EXECUTE AS N'dbo'
	AS
	BEGIN
		DECLARE @user sysname
		DECLARE @dboLogin bit
		EXECUTE AS CALLER;
			SET @user = USER_NAME();
			SET @dboLogin = CONVERT(bit,IS_MEMBER('db_owner'));
		REVERT;
		SELECT
			[Database] = DB_NAME(),
			[Name] = name,
			[ID] = diagram_id,
			[Owner] = USER_NAME(principal_id),
			[OwnerID] = principal_id
		FROM
			sysdiagrams
		WHERE
			(@dboLogin = 1 OR USER_NAME(principal_id) = @user) AND
			(@diagramname IS NULL OR name = @diagramname) AND
			(@owner_id IS NULL OR principal_id = @owner_id)
		ORDER BY
			4, 5, 1
	END
	//
DELIMITER ;

-- Dump della struttura di procedura RomaPass2018.sp_renamediagram
DELIMITER //

	CREATE PROCEDURE dbo.sp_renamediagram
	(
		@diagramname 		sysname,
		@owner_id		int	= null,
		@new_diagramname	sysname
	
	)
	WITH EXECUTE AS 'dbo'
	AS
	BEGIN
		set nocount on
		declare @theId 			int
		declare @IsDbo 			int
		
		declare @UIDFound 		int
		declare @DiagId			int
		declare @DiagIdTarg		int
		declare @u_name			sysname
		if((@diagramname is null) or (@new_diagramname is null))
		begin
			RAISERROR ('Invalid value', 16, 1);
			return -1
		end
	
		EXECUTE AS CALLER;
		select @theId = DATABASE_PRINCIPAL_ID();
		select @IsDbo = IS_MEMBER(N'db_owner'); 
		if(@owner_id is null)
			select @owner_id = @theId;
		REVERT;
	
		select @u_name = USER_NAME(@owner_id)
	
		select @DiagId = diagram_id, @UIDFound = principal_id from dbo.sysdiagrams where principal_id = @owner_id and name = @diagramname 
		if(@DiagId IS NULL or (@IsDbo = 0 and @UIDFound <> @theId))
		begin
			RAISERROR ('Diagram does not exist or you do not have permission.', 16, 1)
			return -3
		end
	
		-- if((@u_name is not null) and (@new_diagramname = @diagramname))	-- nothing will change
		--	return 0;
	
		if(@u_name is null)
			select @DiagIdTarg = diagram_id from dbo.sysdiagrams where principal_id = @theId and name = @new_diagramname
		else
			select @DiagIdTarg = diagram_id from dbo.sysdiagrams where principal_id = @owner_id and name = @new_diagramname
	
		if((@DiagIdTarg is not null) and  @DiagId <> @DiagIdTarg)
		begin
			RAISERROR ('The name is already used.', 16, 1);
			return -2
		end		
	
		if(@u_name is null)
			update dbo.sysdiagrams set [name] = @new_diagramname, principal_id = @theId where diagram_id = @DiagId
		else
			update dbo.sysdiagrams set [name] = @new_diagramname where diagram_id = @DiagId
		return 0
	END
	//
DELIMITER ;

-- Dump della struttura di procedura RomaPass2018.sp_upgraddiagrams
DELIMITER //

	CREATE PROCEDURE dbo.sp_upgraddiagrams
	AS
	BEGIN
		IF OBJECT_ID(N'dbo.sysdiagrams') IS NOT NULL
			return 0;
	
		CREATE TABLE dbo.sysdiagrams
		(
			name sysname NOT NULL,
			principal_id int NOT NULL,	-- we may change it to varbinary(85)
			diagram_id int PRIMARY KEY IDENTITY,
			version int,
	
			definition varbinary(max)
			CONSTRAINT UK_principal_name UNIQUE
			(
				principal_id,
				name
			)
		);


		/* Add this if we need to have some form of extended properties for diagrams */
		/*
		IF OBJECT_ID(N'dbo.sysdiagram_properties') IS NULL
		BEGIN
			CREATE TABLE dbo.sysdiagram_properties
			(
				diagram_id int,
				name sysname,
				value varbinary(max) NOT NULL
			)
		END
		*/

		IF OBJECT_ID(N'dbo.dtproperties') IS NOT NULL
		begin
			insert into dbo.sysdiagrams
			(
				[name],
				[principal_id],
				[version],
				[definition]
			)
			select	 
				convert(sysname, dgnm.[uvalue]),
				DATABASE_PRINCIPAL_ID(N'dbo'),			-- will change to the sid of sa
				0,							-- zero for old format, dgdef.[version],
				dgdef.[lvalue]
			from dbo.[dtproperties] dgnm
				inner join dbo.[dtproperties] dggd on dggd.[property] = 'DtgSchemaGUID' and dggd.[objectid] = dgnm.[objectid]	
				inner join dbo.[dtproperties] dgdef on dgdef.[property] = 'DtgSchemaDATA' and dgdef.[objectid] = dgnm.[objectid]
				
			where dgnm.[property] = 'DtgSchemaNAME' and dggd.[uvalue] like N'_EA3E6268-D998-11CE-9454-00AA00A3F36E_' 
			return 2;
		end
		return 1;
	END
	//
DELIMITER ;

-- Dump della struttura di tabella RomaPass2018.sysdiagrams
CREATE TABLE IF NOT EXISTS "sysdiagrams" (
	"name" NVARCHAR(128) COLLATE Latin1_General_CI_AS,
	"principal_id" INT,
	"diagram_id" INT,
	"version" INT DEFAULT NULL,
	"definition" VARBINARY DEFAULT NULL,
	PRIMARY KEY ("diagram_id"),
	UNIQUE INDEX "UK_principal_name" ("name", "principal_id")
);

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella RomaPass2018.tblCardConsegnate
CREATE TABLE IF NOT EXISTS "tblCardConsegnate" (
	"IDCard" VARCHAR(20) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"IDCodice" INT DEFAULT '(0)',
	"IDProdotti" INT,
	FOREIGN KEY INDEX "FK_tblCardConsegnate_tblCodici" ("IDCodice"),
	PRIMARY KEY ("IDCard", "IDCodice", "IDProdotti"),
	CONSTRAINT "FK_tblCardConsegnate_tblCodici" FOREIGN KEY ("IDCodice") REFERENCES "tblCodici" ("ID") ON UPDATE CASCADE ON DELETE CASCADE
);

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella RomaPass2018.tblCodici
CREATE TABLE IF NOT EXISTS "tblCodici" (
	"ID" INT,
	"IDOrdine" INT,
	"CodiceRitiro" VARCHAR(11) DEFAULT '[dbo].[pbfn_getCodiceRitiro]()' COLLATE Latin1_General_CI_AS,
	"Attivo" BIT DEFAULT '(1)',
	"QuantitaRomaPass" INT DEFAULT '(0)',
	"ImportoRomaPass" NUMERIC(18,2) DEFAULT '(0.0)',
	"QuantitaRomaEPiuPass" INT DEFAULT '(0)',
	"ImportoRomaEPiuPass" NUMERIC(18,2) DEFAULT '(0.0)',
	"QuantitaRomaPass72h" INT DEFAULT '(0)',
	"ImportoRomaPass72h" NUMERIC(18,2) DEFAULT '(0.0)',
	"QuantitaRomaPass48h" INT DEFAULT '(0)',
	"ImportoRomaPass48h" NUMERIC(18,2) DEFAULT '(0.0)',
	"QuantitaRomaPass72h20" INT DEFAULT '(0)',
	"ImportoRomaPass72h20" NUMERIC(18,2) DEFAULT '(0.0)',
	"QuantitaRomaPass48h20" INT DEFAULT '(0)',
	"ImportoRomaPass48h20" NUMERIC(18,2) DEFAULT '(0.0)',
	"QuantitaVox" INT DEFAULT '(0)',
	"ImportoVox" NUMERIC(18,2) DEFAULT '(0.0)',
	"CredenzialiVox" VARCHAR(256) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"Consegnato" BIT DEFAULT '(0)',
	"Data_Consegna" DATETIME2 DEFAULT 'getdate()',
	"IDPuntiRitiroEffettivo" INT DEFAULT '(0)',
	"Nome_Operatore" NVARCHAR(100) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"Nome_Cliente" NVARCHAR(200) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"Documento" NVARCHAR(500) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"InviareKluo" BIT DEFAULT '(0)',
	"AggiornatoKluo" BIT DEFAULT '(0)',
	"GiudizioUniversale" VARCHAR(20) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"CredenzialiVoxNonDisabilitabili" BIT DEFAULT '(0)',
	PRIMARY KEY ("ID"),
	FOREIGN KEY INDEX "FK_tblArticoli_tblOrdini" ("IDOrdine"),
	CONSTRAINT "FK_tblArticoli_tblOrdini" FOREIGN KEY ("IDOrdine") REFERENCES "tblOrdini" ("ID") ON UPDATE CASCADE ON DELETE CASCADE
);

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella RomaPass2018.tblCountries
CREATE TABLE IF NOT EXISTS "tblCountries" (
	"Code" CHAR(2) COLLATE Latin1_General_CI_AS,
	"Name" NVARCHAR(60) COLLATE Latin1_General_CI_AS,
	PRIMARY KEY ("Code")
);

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella RomaPass2018.tblEvents
CREATE TABLE IF NOT EXISTS "tblEvents" (
	"ID" INT,
	"name_it" NVARCHAR(100) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"name_en" NVARCHAR(100) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"new" BIT DEFAULT '(0)',
	"sospeso" BIT DEFAULT '(0)',
	"address_it" NVARCHAR(200) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"address_en" NVARCHAR(200) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"discount_it" NVARCHAR(500) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"discount_en" NVARCHAR(500) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"text_it" NVARCHAR(max) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"text_en" NVARCHAR(max) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"website" VARCHAR(200) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"website_en" VARCHAR(200) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"foto" VARCHAR(200) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"menuID" INT DEFAULT '(0)',
	"publish" BIT DEFAULT '(1)',
	"publish_start" DATETIME DEFAULT 'getdate()',
	"publish_end" DATETIME DEFAULT '''01/01/2100''',
	PRIMARY KEY ("ID")
);

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella RomaPass2018.tblFAQs
CREATE TABLE IF NOT EXISTS "tblFAQs" (
	"ID" INT,
	"section" INT DEFAULT '(0)',
	"title_it" NVARCHAR(500) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"title_en" NVARCHAR(500) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"new" BIT DEFAULT '(0)',
	"text_it" NVARCHAR(max) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"text_en" NVARCHAR(max) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"order" INT DEFAULT '(0)',
	"publish" BIT DEFAULT '(1)',
	"publish_start" DATETIME2(2) DEFAULT 'getdate()',
	"publish_end" DATETIME2(2) DEFAULT '''2100-01-01''',
	PRIMARY KEY ("ID")
);

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella RomaPass2018.tblFAQSections
CREATE TABLE IF NOT EXISTS "tblFAQSections" (
	"ID" INT,
	"title_it" NVARCHAR(200) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"title_en" NVARCHAR(200) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"order" INT DEFAULT '(0)',
	PRIMARY KEY ("ID")
);

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella RomaPass2018.tblGiudizioUniversale
CREATE TABLE IF NOT EXISTS "tblGiudizioUniversale" (
	"ID" INT,
	"Codice" VARCHAR(20) COLLATE Latin1_General_CI_AS,
	"Numerico" INT,
	"Usato" BIT DEFAULT '(0)',
	PRIMARY KEY ("ID")
);

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella RomaPass2018.tblLogs
CREATE TABLE IF NOT EXISTS "tblLogs" (
	"ID" INT,
	"action" VARCHAR(50) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"template" VARCHAR(50) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"userID" INT DEFAULT '(0)',
	"documentID" INT DEFAULT '(0)',
	"parameters" VARCHAR(1000) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"IP" VARCHAR(50) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"date" DATETIME DEFAULT 'getdate()',
	PRIMARY KEY ("ID")
);

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella RomaPass2018.tblLogsOT
CREATE TABLE IF NOT EXISTS "tblLogsOT" (
	"ID" INT,
	"OTID" INT,
	"action" VARCHAR(50) COLLATE Latin1_General_CI_AS,
	"actionID" INT,
	"IP" VARCHAR(15) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"data" DATETIME2(2) DEFAULT 'getdate()',
	PRIMARY KEY ("ID"),
	FOREIGN KEY INDEX "FK_tblLogsOT_tblOperatoriTuristici" ("OTID"),
	CONSTRAINT "FK_tblLogsOT_tblOperatoriTuristici" FOREIGN KEY ("OTID") REFERENCES "tblOperatoriTuristici" ("ID") ON UPDATE CASCADE ON DELETE CASCADE
);

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella RomaPass2018.tblMenu
CREATE TABLE IF NOT EXISTS "tblMenu" (
	"ID" INT,
	"fatherID" INT DEFAULT '(0)',
	"label" VARCHAR(50) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"name_it" NVARCHAR(50) DEFAULT 'N''''' COLLATE Latin1_General_CI_AS,
	"name_en" NVARCHAR(50) DEFAULT 'N''''' COLLATE Latin1_General_CI_AS,
	"name_long_it" NVARCHAR(200) DEFAULT 'N''''' COLLATE Latin1_General_CI_AS,
	"name_long_en" NVARCHAR(200) DEFAULT 'N''''' COLLATE Latin1_General_CI_AS,
	"page_it" VARCHAR(100) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"page_en" VARCHAR(100) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"page_behind" VARCHAR(10) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"template" VARCHAR(50) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"css_class" VARCHAR(50) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"bottom" BIT DEFAULT '(0)',
	"hidden" BIT DEFAULT '(0)',
	"oldID" INT DEFAULT '(0)',
	"oldTemplate" VARCHAR(50) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"order" INT DEFAULT '(0)',
	"publish" BIT DEFAULT '(1)',
	"publish_start" DATETIME2(2) DEFAULT 'getdate()',
	"publish_end" DATETIME2(2) DEFAULT '''2100-01-01''',
	PRIMARY KEY ("ID"),
	UNIQUE INDEX "IX_tblMenu_1" ("label")
);

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella RomaPass2018.tblMuseums
CREATE TABLE IF NOT EXISTS "tblMuseums" (
	"ID" INT,
	"name_it" NVARCHAR(200) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"name_en" NVARCHAR(200) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"new" BIT DEFAULT '(0)',
	"address_it" NVARCHAR(300) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"address_en" NVARCHAR(300) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"text_it" NVARCHAR(max) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"text_en" NVARCHAR(max) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"remarks_it" NVARCHAR(1000) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"remarks_en" NVARCHAR(1000) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"website" VARCHAR(200) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"website_en" VARCHAR(200) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"foto" VARCHAR(200) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"menuID" INT DEFAULT '(0)',
	"alpha_name_it" NVARCHAR(100) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"alpha_name_en" NVARCHAR(100) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"publish" BIT DEFAULT '(1)',
	"publish_start" DATETIME DEFAULT 'getdate()',
	"publish_end" DATETIME DEFAULT '''01/01/2100''',
	PRIMARY KEY ("ID")
);

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella RomaPass2018.tblNews
CREATE TABLE IF NOT EXISTS "tblNews" (
	"ID" INT,
	"date" DATE DEFAULT 'getdate()',
	"date_end" DATE DEFAULT '''1900-01-01''',
	"text_it" NVARCHAR(800) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"text_en" NVARCHAR(800) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"link_it" VARCHAR(200) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"link_en" VARCHAR(200) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"publish" BIT DEFAULT '(1)',
	"publish_start" DATETIME2(2) DEFAULT 'getdate()',
	"publish_end" DATETIME2(2) DEFAULT '''2100-01-01''',
	PRIMARY KEY ("ID")
);

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella RomaPass2018.tblOperatoriBackoffice
CREATE TABLE IF NOT EXISTS "tblOperatoriBackoffice" (
	"ID" INT,
	"Nome_Operatore" NVARCHAR(100) COLLATE Latin1_General_CI_AS,
	"username" NVARCHAR(50) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"password" NVARCHAR(50) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"Attivo" BIT DEFAULT '(1)',
	"InizioValidita" DATETIME DEFAULT 'getdate()',
	"FineValidita" DATETIME DEFAULT '''2100-01-01 00:00:00''',
	PRIMARY KEY ("ID")
);

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella RomaPass2018.tblOperatoriTuristici
CREATE TABLE IF NOT EXISTS "tblOperatoriTuristici" (
	"ID" INT,
	"Nome_Agenzia" NVARCHAR(100) COLLATE Latin1_General_CI_AS,
	"username" NVARCHAR(50) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"password" NVARCHAR(50) DEFAULT '[dbo].[pbfn_getPwd]()' COLLATE Latin1_General_CI_AS,
	"pwdexpire" DATETIME DEFAULT '''1900-01-01''',
	"pwdhash" BINARY DEFAULT '0x0000000000000000000000000000000000000000000000000000000000000000',
	"email" NVARCHAR(50) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"Attivo" BIT DEFAULT '(0)',
	"InizioValidita" DATETIME DEFAULT 'getdate()',
	"FineValidita" DATETIME DEFAULT '''2023-12-31 23:59:59.997''',
	"CodiceMultiplo" BIT DEFAULT '(0)',
	"CodiceSingolo" BIT DEFAULT '(1)',
	"PrezzoRP72h20" NUMERIC(18,2) DEFAULT '(50.0)',
	"PrezzoRP48h20" NUMERIC(18,2) DEFAULT '(29.80)',
	"PrezzoVox" NUMERIC(18,2) DEFAULT '(4.0)',
	"PrezzoRP72h202" NUMERIC(18,2) DEFAULT '(50.0)',
	"PrezzoRP48h202" NUMERIC(18,2) DEFAULT '(29.80)',
	"MaxRP72h20" INT DEFAULT '(50)',
	"MaxRP48h20" INT DEFAULT '(0)',
	"Indirizzo" NVARCHAR(100) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"CAP" VARCHAR(50) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"Citta" NVARCHAR(50) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"Provincia" VARCHAR(50) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"Nazione" VARCHAR(50) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"Telefono" VARCHAR(50) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"Fax" VARCHAR(50) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"Nome_Contatto" VARCHAR(50) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"CF_PIVA" VARCHAR(50) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"Note" NTEXT DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"ResetPwd" BIT DEFAULT '(1)',
	PRIMARY KEY ("ID")
);

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella RomaPass2018.tblOrdini
CREATE TABLE IF NOT EXISTS "tblOrdini" (
	"ID" INT,
	"CodiceRitiro" VARCHAR(11) DEFAULT '[dbo].[pbfn_getCodiceRitiro]()' COLLATE Latin1_General_CI_AS,
	"PagamentoEffettuato" BIT DEFAULT '(0)',
	"Bonifico" BIT DEFAULT '(0)',
	"DataRitiro" DATETIME DEFAULT 'dateadd(day,(1),getdate())',
	"Data" DATETIME DEFAULT 'getdate()',
	"ImportoTotale" NUMERIC(18,2) DEFAULT '(0.0)',
	"IDSupplemento" INT DEFAULT '(0)',
	"IDOperatoreTuristico" INT DEFAULT '(0)',
	"IDPuntiRitiro" INT DEFAULT '(0)',
	"Lingua" CHAR(2) DEFAULT '''it''' COLLATE Latin1_General_CI_AS,
	"Nome" NVARCHAR(100) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"Cognome" NVARCHAR(100) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"Localita" NVARCHAR(100) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"Provincia" NVARCHAR(50) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"Nazione" NVARCHAR(50) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"Telefono" NVARCHAR(50) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"Email" NVARCHAR(50) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"CodiceFiscale" NVARCHAR(50) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"IP" VARCHAR(50) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"Note_Consegna" NVARCHAR(max) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"PagamentoChecked" BIT DEFAULT '(0)',
	"InviareKluo" BIT DEFAULT '(0)',
	"Marketing" BIT DEFAULT '(0)',
	"Regolamento" BIT DEFAULT '(1)',
	"Privacy" BIT DEFAULT '(1)',
	PRIMARY KEY ("ID"),
	FOREIGN KEY INDEX "FK_tblOrdini_tblOperatoriTuristici" ("IDOperatoreTuristico"),
	FOREIGN KEY INDEX "FK_tblOrdini_tblPuntiRitiro" ("IDPuntiRitiro"),
	CONSTRAINT "FK_tblOrdini_tblOperatoriTuristici" FOREIGN KEY ("IDOperatoreTuristico") REFERENCES "tblOperatoriTuristici" ("ID") ON UPDATE NO_ACTION ON DELETE NO_ACTION,
	CONSTRAINT "FK_tblOrdini_tblPuntiRitiro" FOREIGN KEY ("IDPuntiRitiro") REFERENCES "tblPuntiRitiro" ("ID") ON UPDATE NO_ACTION ON DELETE NO_ACTION
);

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella RomaPass2018.tblProvince
CREATE TABLE IF NOT EXISTS "tblProvince" (
	"Sigla" CHAR(2) COLLATE Latin1_General_CI_AS,
	"Provincia" VARCHAR(50) COLLATE Latin1_General_CI_AS,
	"Regione" VARCHAR(50) COLLATE Latin1_General_CI_AS,
	PRIMARY KEY ("Sigla")
);

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella RomaPass2018.tblPuntiRitiro
CREATE TABLE IF NOT EXISTS "tblPuntiRitiro" (
	"ID" INT,
	"Nome" NVARCHAR(100) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"Indirizzo" NVARCHAR(200) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"Orario" NVARCHAR(500) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"PuntoZetema" BIT DEFAULT '(0)',
	"EMail" VARCHAR(50) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"Attivo" BIT DEFAULT '(1)',
	"Password" VARCHAR(50) DEFAULT '[dbo].[pbfn_getPwd]()' COLLATE Latin1_General_CI_AS,
	"CodiceKluo" CHAR(2) DEFAULT '''--''' COLLATE Latin1_General_CI_AS,
	"Importanza" INT DEFAULT '(0)',
	"IDOperatoreTuristico" INT DEFAULT '(0)',
	FOREIGN KEY INDEX "FK_tblPuntiRitiro_tblOperatoriTuristici" ("IDOperatoreTuristico"),
	PRIMARY KEY ("ID"),
	CONSTRAINT "FK_tblPuntiRitiro_tblOperatoriTuristici" FOREIGN KEY ("IDOperatoreTuristico") REFERENCES "tblOperatoriTuristici" ("ID") ON UPDATE NO_ACTION ON DELETE NO_ACTION
);

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella RomaPass2018.tblRoutes
CREATE TABLE IF NOT EXISTS "tblRoutes" (
	"ID" INT,
	"name_it" NVARCHAR(100) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"name_en" NVARCHAR(100) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"new" BIT DEFAULT '(0)',
	"sospeso" BIT DEFAULT '(0)',
	"address_it" NVARCHAR(200) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"address_en" NVARCHAR(200) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"discount_it" NVARCHAR(500) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"discount_en" NVARCHAR(500) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"text_it" NVARCHAR(max) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"text_en" NVARCHAR(max) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"website" VARCHAR(200) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"website_en" VARCHAR(200) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"foto" VARCHAR(200) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"menuID" INT DEFAULT '(0)',
	"publish" BIT DEFAULT '(1)',
	"publish_start" DATETIME DEFAULT 'getdate()',
	"publish_end" DATETIME DEFAULT '''01/01/2100''',
	PRIMARY KEY ("ID")
);

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella RomaPass2018.tblServices
CREATE TABLE IF NOT EXISTS "tblServices" (
	"ID" INT,
	"name_it" NVARCHAR(100) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"name_en" NVARCHAR(100) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"new" BIT DEFAULT '(0)',
	"sospeso" BIT DEFAULT '(0)',
	"address_it" NVARCHAR(200) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"address_en" NVARCHAR(200) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"discount_it" NVARCHAR(500) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"discount_en" NVARCHAR(500) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"text_it" NVARCHAR(max) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"text_en" NVARCHAR(max) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"website" VARCHAR(200) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"website_en" VARCHAR(200) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"foto" VARCHAR(200) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"menuID" INT DEFAULT '(0)',
	"publish" BIT DEFAULT '(1)',
	"publish_start" DATETIME DEFAULT 'getdate()',
	"publish_end" DATETIME DEFAULT '''01/01/2100''',
	PRIMARY KEY ("ID")
);

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella RomaPass2018.tblSlides
CREATE TABLE IF NOT EXISTS "tblSlides" (
	"ID" INT,
	"name_it" NVARCHAR(100) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"name_en" NVARCHAR(100) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"image" VARCHAR(50) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"order" INT DEFAULT '(0)',
	"publish" BIT DEFAULT '(1)',
	"publish_start" DATETIME2(2) DEFAULT 'getdate()',
	"publish_end" DATETIME2(2) DEFAULT '''2100-01-01''',
	PRIMARY KEY ("ID")
);

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella RomaPass2018.tblTexts
CREATE TABLE IF NOT EXISTS "tblTexts" (
	"ID" INT,
	"title_it" NVARCHAR(200) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"title_en" NVARCHAR(200) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"launch_it" NVARCHAR(2000) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"launch_en" NVARCHAR(2000) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"text_it" NVARCHAR(max) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"text_en" NVARCHAR(max) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"menuID" INT DEFAULT '(0)',
	"order" INT DEFAULT '(0)',
	"publish" BIT DEFAULT '(1)',
	"publish_start" DATETIME2(2) DEFAULT 'getdate()',
	"publish_end" DATETIME2(2) DEFAULT '''2100-01-01''',
	PRIMARY KEY ("ID")
);

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella RomaPass2018.tblVideos
CREATE TABLE IF NOT EXISTS "tblVideos" (
	"ID" INT,
	"title_it" NVARCHAR(200) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"title_en" NVARCHAR(200) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"text_it" NVARCHAR(max) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"text_en" NVARCHAR(max) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"embed_site" VARCHAR(50) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"embed_id" VARCHAR(50) DEFAULT '''''' COLLATE Latin1_General_CI_AS,
	"order" INT DEFAULT '(0)',
	"publish" BIT DEFAULT '(1)',
	"publish_start" DATETIME DEFAULT 'getdate()',
	"publish_end" DATETIME DEFAULT '''01/01/2100''',
	PRIMARY KEY ("ID")
);

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di trigger RomaPass2018.tr_cercaCodici
/* Errore SQL (156): Incorrect syntax near the keyword 'FROM'. */-- Dump della struttura di trigger RomaPass2018.tr_cercaOrdini
/* Errore SQL (156): Incorrect syntax near the keyword 'FROM'. */-- Dump della struttura di trigger RomaPass2018.tr_orderVideos
/* Errore SQL (156): Incorrect syntax near the keyword 'FROM'. */-- Dump della struttura di trigger RomaPass2018.tr_SincroKluo_CodiciAfterDelete
/* Errore SQL (156): Incorrect syntax near the keyword 'FROM'. */-- Dump della struttura di trigger RomaPass2018.tr_SincroKluo_CodiciUpdate
/* Errore SQL (156): Incorrect syntax near the keyword 'FROM'. */-- Dump della struttura di trigger RomaPass2018.tr_SincroKluo_OrderInsteadOfDelete
/* Errore SQL (156): Incorrect syntax near the keyword 'FROM'. */-- Dump della struttura di trigger RomaPass2018.tr_SincroKluo_OrderUpdate
/* Errore SQL (156): Incorrect syntax near the keyword 'FROM'. */-- Dump della struttura di trigger RomaPass2018.tr_UpdateDataConsegna
/* Errore SQL (156): Incorrect syntax near the keyword 'FROM'. */-- Rimozione temporanea di tabella e creazione della struttura finale della vista
DROP TABLE IF EXISTS "pbv_getRandNumber";

CREATE VIEW [dbo].[pbv_getRandNumber]
AS
SELECT RAND() as RandNumber


;


-- Dump della struttura del database ZPeC
CREATE DATABASE IF NOT EXISTS "ZPeC";
USE "ZPeC";

-- Dump della struttura di tabella ZPeC.Albo
CREATE TABLE IF NOT EXISTS "Albo" 
);

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella ZPeC.AlboProfili
CREATE TABLE IF NOT EXISTS "AlboProfili" 
);

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella ZPeC.Allegati
CREATE TABLE IF NOT EXISTS "Allegati" 
);

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella ZPeC.Commenti
CREATE TABLE IF NOT EXISTS "Commenti" 
);

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella ZPeC.Istruzione
CREATE TABLE IF NOT EXISTS "Istruzione" 
);

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella ZPeC.IT
CREATE TABLE IF NOT EXISTS "IT" 
);

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella ZPeC.ITElenco
CREATE TABLE IF NOT EXISTS "ITElenco" 
);

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella ZPeC.LavoroAut
CREATE TABLE IF NOT EXISTS "LavoroAut" 
);

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella ZPeC.LavoroSub
CREATE TABLE IF NOT EXISTS "LavoroSub" 
);

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella ZPeC.Lingue
CREATE TABLE IF NOT EXISTS "Lingue" 
);

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella ZPeC.LingueElenco
CREATE TABLE IF NOT EXISTS "LingueElenco" 
);

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella ZPeC.Logs
CREATE TABLE IF NOT EXISTS "Logs" 
);

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella ZPeC.LogsAdmin
CREATE TABLE IF NOT EXISTS "LogsAdmin" 
);

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella ZPeC.Profili
CREATE TABLE IF NOT EXISTS "Profili" 
);

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella ZPeC.Province
CREATE TABLE IF NOT EXISTS "Province" 
);

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella ZPeC.Ricerche
CREATE TABLE IF NOT EXISTS "Ricerche" 
);

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella ZPeC.sysdiagrams
CREATE TABLE IF NOT EXISTS "sysdiagrams" (
	"name" NVARCHAR(128) COLLATE Latin1_General_CI_AS,
	"principal_id" INT,
	"diagram_id" INT,
	"version" INT DEFAULT NULL,
	"definition" VARBINARY DEFAULT NULL,
	PRIMARY KEY ("diagram_id"),
	UNIQUE INDEX "UK_principal_name" ("name", "principal_id")
);

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella ZPeC.ulkISO3166
CREATE TABLE IF NOT EXISTS "ulkISO3166" 
);

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di trigger ZPeC.tr_AlboProfili
/* Errore SQL (156): Incorrect syntax near the keyword 'FROM'. */-- Dump della struttura di trigger ZPeC.tr_Allegati
/* Errore SQL (156): Incorrect syntax near the keyword 'FROM'. */-- Dump della struttura di trigger ZPeC.tr_Commenti
/* Errore SQL (156): Incorrect syntax near the keyword 'FROM'. */-- Dump della struttura di trigger ZPeC.tr_Istruzione
/* Errore SQL (156): Incorrect syntax near the keyword 'FROM'. */-- Dump della struttura di trigger ZPeC.tr_IT
/* Errore SQL (156): Incorrect syntax near the keyword 'FROM'. */-- Dump della struttura di trigger ZPeC.tr_ITElenco
/* Errore SQL (156): Incorrect syntax near the keyword 'FROM'. */-- Dump della struttura di trigger ZPeC.tr_LavoroAut
/* Errore SQL (156): Incorrect syntax near the keyword 'FROM'. */-- Dump della struttura di trigger ZPeC.tr_LavoroSub
/* Errore SQL (156): Incorrect syntax near the keyword 'FROM'. */-- Dump della struttura di trigger ZPeC.tr_Lingue
/* Errore SQL (156): Incorrect syntax near the keyword 'FROM'. */-- Dump della struttura di trigger ZPeC.tr_Profili
/* Errore SQL (156): Incorrect syntax near the keyword 'FROM'. *//*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
