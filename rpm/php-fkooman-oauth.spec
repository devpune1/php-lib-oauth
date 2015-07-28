%global composer_vendor  fkooman
%global composer_project oauth

%global github_owner     fkooman
%global github_name      php-lib-oauth

Name:       php-%{composer_vendor}-%{composer_project}
Version:    1.0.0
Release:    1%{?dist}
Summary:    OAuth 2.0 Authorization Server library

Group:      System Environment/Libraries
License:    ASL 2.0
URL:        https://github.com/%{github_owner}/%{github_name}
Source0:    https://github.com/%{github_owner}/%{github_name}/archive/%{version}.tar.gz
BuildArch:  noarch

Provides:   php-composer(%{composer_vendor}/%{composer_project}) = %{version}

Requires:   php(language) >= 5.3.3
Requires:   php-filter
Requires:   php-pcre
Requires:   php-pdo
Requires:   php-standard

%description
OAuth 2.0 Authorization Server library.

%prep
%setup -qn %{github_name}-%{version}

%build

%install
mkdir -p ${RPM_BUILD_ROOT}%{_datadir}/php
cp -pr src/* ${RPM_BUILD_ROOT}%{_datadir}/php

%files
%defattr(-,root,root,-)
%dir %{_datadir}/php/%{composer_vendor}/OAuth
%{_datadir}/php/%{composer_vendor}/OAuth
%doc README.md CHANGES.md composer.json
%license COPYING

%changelog
* Thu Jul 23 2015 François Kooman <fkooman@tuxed.net> - 1.0.0-1
- update to 1.0.0
