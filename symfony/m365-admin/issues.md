# 1.

### Switch to an account that has permission

```
Switch to an account that has permission

Your account doesn't have permission to view or manage this page

Login is not supported for consumer users without business presence.

```

```
Prihlásiť sa
S osobným kontom sa sem nemôžete prihlásiť. Použite namiesto toho pracovné alebo školské konto.

```


- ### [Create new account](https://partner.microsoft.com/en-us/dashboard/account/v3/enrollment/introduction/partnership)

- ### [Video tutorial](https://www.youtube.com/watch?v=4gTW9_-qjjM&ab_channel=RavindraJadhav)

- User: matejbrodziansky-imatic
- Pass: JQzaH7daBv*2Npq
- Logged: matejbrodziansky-imatic@matejbrodziansky.onmicrosoft.com

Now you can acces to  [Microsoft 365 admin center](https://admin.microsoft.com/Adminportal/Home#/homepage)


# 2. 

Lavý panel -> Settings -> Integrated apps 

[Integrated apps](https://admin.microsoft.com/Adminportal/Home#/Settings/IntegratedApps)



Discover, purchase, acquire, manage, and deploy Microsoft 365 Apps developed by Microsoft partners. You can also deploy and manage Line-of-business add-ins developed within your organization.
## Pokračoval som sem 
For advanced management of these apps go to the respective admin center or page :
[Azure Active Directory](https://entra.microsoft.com/matejbrodziansky.onmicrosoft.com/#view/Microsoft_AAD_IAM/TenantOverview.ReactView?Microsoft_AAD_IAM_legacyAADRedirect=true)

# 3.

[Centrum pro správu Microsoft Entra](https://entra.microsoft.com/#view/Microsoft_AAD_IAM/TenantOverview.ReactView?Microsoft_AAD_IAM_legacyAADRedirect=true)

Menu -> Aplikace -> Registrace aplikací .

"+" Nová registrace

### FORM 
#### Podporované typy účtu
- Účty v libovolném adresáři organizace (libovolný tenant Microsoft Entra ID – více tenantů)


#### Identifikátor URI pro přesměrování (nepovinné)
Až se uživatel úspěšně ověří, vrátíme odpověď ověřování na tento identifikátor URI. V tomto okamžiku je zadání nepovinné a dá se změnit později, ale u většiny scénářů ověřování se vyžaduje nějaká hodnota.
- web 
- <yourdomain.com>/oauth-callback

Vytvoria sa ID, je potrebné ich vložiť do .env súboru
```
ID aplikace (klienta): 
ID objektu: 
ID adresáře (tenanta): 
```

Do env súboru vložiť
- MICROSOFT_CLIENT_ID = ID aplikace (klienta)
- MICROSOFT_TENANT_ID = ID adresáře (tenanta)



# 4.
Vlavo je menu aplikácie -> Certifikáty a tajné kódy

Tajné kódy klienta (1) -> Nový tajný kód klienta  -> Vytvoriť

Vráti 
```
Popis
Platnost vyprší
Hodnota
Tajné ID
```

Do env súboru vložiť
- MICROSOFT_CLIENT_SECRET = Hodnota

# 5. Oprávnění rozhraní API
V menu aplikace -> Oprávnění rozhraní API -> Přidat oprávnění -> Microsoft Graph -> Delegované oprávnění -> User.Read + GROUP
![img_1.png](img_1.png)
![img_2.png](img_2.png)
![img_3.png](img_3.png)
Udelil som pre Graph API
Microsoft Graph (16)

**Application.Read.All**
- Aplikace
- Read all applications
- Ano
- Uděleno pro Matej

**Application.ReadWrite.All**
- Aplikace
- Read and write all applications
- Ano
- Uděleno pro Matej

**Application.ReadWrite.OwnedBy**
- Aplikace
- Manage apps that this app creates or owns
- Ano
- Uděleno pro Matej

**Group.Create**
- Aplikace
- Create groups
- Ano
- Uděleno pro Matej

**Group.Read.All**
- Aplikace
- Read all groups
- Ano
- Uděleno pro Matej

**Group.ReadWrite.All**
- Aplikace
- Read and write all groups
- Ano
- Uděleno pro Matej

**GroupMember.Read.All**
- Aplikace
- Read all group memberships
- Ano
- Uděleno pro Matej

**GroupMember.ReadWrite.All**
- Aplikace
- Read and write all group memberships
- Ano
- Uděleno pro Matej

**User.EnableDisableAccount.All**
- Aplikace
- Enable and disable user accounts
- Ano
- Uděleno pro Matej

**User.Export.All**
- Aplikace
- Export user's data
- Ano
- Uděleno pro Matej

**User.Invite.All**
- Aplikace
- Invite guest users to the organization
- Ano
- Uděleno pro Matej

**User.ManageIdentities.All**
- Aplikace
- Manage all users' identities
- Ano
- Uděleno pro Matej

**User.Read**
- Delegováno
- Sign in and read user profile
- Ne
- Uděleno pro Matej

**User.Read.All**
- Aplikace
- Read all users' full profiles
- Ano
- Uděleno pro Matej

**User.ReadBasic.All**
- Aplikace
- Read all users' basic profiles
- Ano
- Uděleno pro Matej

**User.ReadWrite.All**
- Aplikace
- Read and write all users' full profiles
- Ano
- Uděleno pro Matej


Pre udeleno pro Matej je treba click "Microsoft Graph (16)" ktoré pribudlo v zozname (je potreba refresh page ) a potom v pravom paneli "Command-Udělit souhlas správce pro Matej" inak API nebude fungovať.
![img.png](img.png)




# 6.
V menu aplikace -> Ověřování -> Přístupové tokeny (používané pro implicitní toky)
![img_4.png](img_4.png)

+ Web call back URL 
```
<your-domain>/microsoft/callback
```

![img_5.png](img_5.png)




### Important Links
(Microsoft package)[https://packagist.org/packages/microsoft/microsoft-graph]
[Group methods](https://learn.microsoft.com/en-us/graph/api/resources/group?view=graph-rest-1.0#methods)
[User methods](https://learn.microsoft.com/en-us/graph/api/resources/user?view=graph-rest-1.0#methods)
[User API  methods](https://learn.microsoft.com/en-us/graph/api/resources/user?view=graph-rest-1.0)
[Add member to group ](https://learn.microsoft.com/en-us/graph/api/group-post-members?view=graph-rest-1.0&tabs=http)
### PHP CODES
[User API  methods](https://learn.microsoft.com/en-us/graph/api/resources/user?view=graph-rest-1.0)
[List a user's direct memberships](https://learn.microsoft.com/en-us/graph/api/user-list-memberof?view=graph-rest-1.0&tabs=php)
[Add members](https://learn.microsoft.com/en-us/graph/api/group-post-members?view=graph-rest-1.0&tabs=php)



http://localhost:8000/azure/callback
238545f8-6039-439e-8348-c98077b1c8ca
https://learn.microsoft.com/en-us/graph/api/group-post-members?view=graph-rest-1.0&tabs=php#request-headers