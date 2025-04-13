<?php

return [

    'call_note' => [
        'resource' => [
            'group' => 'Anrufnotizen',
            'name' => 'Anrufnotiz',
            'name_plural' => 'Anrufnotizen',
        ],
        'form' => [
            'section_general' => 'Allgemein',
            'field_name' => 'Titel der Anrufnotiz',
            'field_description' => 'Beschreibung',
            'field_call_id' => 'Anruf',
        ],
        'table' => [
            'name' => 'Titel',
            'description' => 'Beschreibung',
            'call' => 'Anruf',
            'filter_call' => 'Anruf filtern',
        ],
    ],
    'call' => [
        'resource' => [
            'group' => 'Anrufe',
            'name' => 'Anruf',
            'name_plural' => 'Anrufe',
        ],
        'form' => [
            'section_general' => 'Allgemein',
            'section_contract' => 'Auftrag',
            'field_name' => 'Name',
            'field_description' => 'Beschreibung',
            'field_is_done' => 'Erledigt',
            'field_on_date' => 'Datum',
            'field_contract' => 'Auftrag',
        ],
        'table' => [
            'field_on_date' => 'Datum',
            'field_customer' => 'Kunde',
            'field_contract' => 'Auftrag',
            'field_user' => 'Benutzer',
            'field_is_done' => 'Erledigt',
            'filter_is_done' => 'Erledigt Status',
            'filter_on_date' => 'Datum Filter',
            'filter_from' => 'Von',
            'filter_until' => 'Bis',
            'filter_contract' => 'Auftrag',
        ],
    ],
    'contract_note' => [
        'resource' => [
            'group' => 'Aufträge', // Navigation group
            'name' => 'Auftragsnotiz', // Singular resource name
            'name_plural' => 'Auftragsnotizen', // Plural resource name
        ],
        'form' => [
            'section_general' => 'Allgemeine Informationen',
            'section_contract' => 'Auftrag',
            'field_name' => 'Titel der Notiz',
            'field_description' => 'Beschreibung',
            'field_date' => 'Datum',
            'field_attachments' => 'Anhänge',
            'field_contract' => "Auftrag",
        ],
        'table' => [
            'name' => 'Titel',
            'date' => 'Datum',
            'contract' => 'Auftrag',
            'customer' => 'Kunde',
        ],
    ],
    'contract' => [
        'resource' => [
            'group' => 'Aufträge',
            'name' => 'Auftrag',
            'name_plural' => 'Aufträge',
        ],
        'form' => [
            'section_general' => 'Allgemein',
            'section_location' => 'Standort',
            'section_customer' => 'Kunde',
            'section_employees' => 'Mitarbeiter',
            'section_attachments' => 'Anhänge',
            'field_name' => 'Name',
            'field_description' => 'Beschreibung',
            'field_priority' => 'Priorität',
            'field_due_to' => 'Fällig am',
            'field_is_finished' => 'Erledigt',
            'field_customer' => 'Kunde',
            'field_users' => 'Mitarbeiter',
            'field_country' => 'Land',
            'field_state' => 'Bundesland',
            'field_city' => 'Stadt',
            'field_zip_code' => 'PLZ',
            'field_address' => 'Adresse',
            'field_attachments' => 'Anhänge',
        ],
        'table' => [
            'name' => 'Name',
            'description' => 'Beschreibung',
            'priority' => 'Priorität',
            'due_to' => 'Fällig am',
            'is_finished' => 'Erledigt',
            'customer' => 'Kunde',
            'filter_priority' => 'Priorität',
            'filter_customer' => 'Kunde',
            'filter_users' => 'Mitarbeiter',
            'filter_due_to' => 'Fälligkeitszeitraum',
            'filter_is_finished' => 'Erledigt',
            'filter_name' => 'Firmenname des Auftrags',
        ],
    ],
    'customer' => [
        'resource' => [
            'group' => 'Aufträge',
            'name' => 'Kunde',
            'name_plural' => 'Kunden',
        ],
        'form' => [
            'section_general' => 'Allgemein',
            'section_address' => 'Adresse',
            'field_full_name' => 'Vollständiger Name',
            'field_company_name' => 'Firmenname',
            'field_email' => 'E-Mail',
            'field_phone' => 'Telefon',
            'field_tax_id' => 'Steuer-ID',
            'field_country' => 'Land',
            'field_state' => 'Bundesland',
            'field_city' => 'Stadt',
            'field_zip_code' => 'PLZ',
            'field_address' => 'Adresse',
        ],
        'table' => [
            'company_name' => 'Firmenname',
            'email' => 'E-Mail',
            'phone' => 'Telefon',
            'city' => 'Stadt',
            'filter_country' => 'Land',
            'filter_state' => 'Bundesland',
            'filter_city' => 'Stadt',
        ],
    ],
    'login_credentials' => [
        'resource' => [
            'group' => 'Aufträge',
            'name' => 'Login-Daten',
            'name_plural' => 'Login-Daten',
        ],
        'form' => [
            'section_general' => 'Allgemein',
            'field_name' => 'Name',
            'field_description' => 'Beschreibung',
            'field_email' => 'E-Mail',
            'field_password' => 'Passwort',
            'field_attachments' => 'Anhänge',
            'section_contracts' => 'Aufträge', // Added section_contracts

        ],
        'table' => [
            'name' => 'Name',
            'contracts' => 'Aufträge',
            'description' => 'Beschreibung',
        ],
        'filter' => [
            'email' => [
                'label' => 'E-Mail-Domain',
                'placeholder' => 'Geben Sie die Domain ein (z.B. example.com)',
            ],
            'name' => [
                'label' => 'Name',
                'placeholder' => 'Nach Namen suchen',
            ],
        ],
    ],
    'permission' => [
        'resource' => [
            'group' => 'Einstellungen',
            'name' => 'Berechtigung',
            'name_plural' => 'Berechtigungen',
        ],
        'form' => [
            'section_general' => 'Allgemein',
            'field_name' => 'Name',
        ],
        'table' => [
            'name' => 'Name',
        ],
    ],
    'user' => [
        'resource' => [
            'group' => 'Einstellungen',
            'name' => 'Benutzer',
            'name_plural' => 'Benutzer',
        ],
        'form' => [
            'section_general' => 'Allgemein',
            'field_name' => 'Name',
            'field_email' => 'E-Mail',
            'field_password' => 'Passwort',
            'field_roles' => 'Rollen',
        ],
        'table' => [
            'name' => 'Name',
            'email' => 'E-Mail',
            'created_at' => 'Erstellt am',
            'updated_at' => 'Aktualisiert am',
        ],
    ],
    'role' => [
        'resource' => [
            'group' => 'Einstellungen',
            'name' => 'Rolle',
            'name_plural' => 'Rollen',
        ],
        'form' => [
            'section_general' => 'Allgemein',
            'field_name' => 'Name',
            'field_permissions' => 'Berechtigungen',
        ],
        'table' => [
            'id' => 'ID',
            'name' => 'Name',
            'created_at' => 'Erstellt am',
        ],
    ],
    'todo' => [
        'resource' => [
            'group' => 'Aufträge',
            'name' => 'To-Do',
            'name_plural' => 'To-Dos',
        ],
        'form' => [
            'section_general' => 'Allgemein',
            'field_name' => 'Name',
            'field_due_to' => 'Fälligkeitsdatum',
            'field_description' => 'Beschreibung',
            'field_is_done' => 'Abgeschlossen',
            'field_priority_label' => 'Priorität',
            'field_priority' => [
                'low' => 'Low',
                'medium' => 'Medium',
                'high' => 'High',
            ],
            'field_attachments' => 'Anhänge',
            'section_contract' => 'Aufträge',
            'field_contract_classification' => 'Auftrag',
        ],
        'table' => [
            'name' => 'Name',
            'contract' => 'Auftrag',
            'customer' => 'Kunde',
            'due_to' => 'Fälligkeitsdatum',
            'priority' => 'Priorität',
            'is_done' => 'Abgeschlossen',
        ],
    ],
    'time' => [
        'resource' => [
            'group' => 'Aufträge',  // Navigation group label
            'name' => 'Zeiteintrag',      // Singular name for the resource
            'name_plural' => 'Zeiteinträge', // Plural name for the resource
        ],
        'form' => [
            'general' => 'Allgemein',
            'field_date' => 'Datum*',
            'field_description' => 'Beschreibung',

            'time' => 'Zeit',
            'field_total_hours_worked' => 'Gesamtarbeitsstunden*',
            'field_total_minutes_worked' => 'Gesamtminuten',

            'contract' => 'Auftrag',
            'field_contract_label' => 'Auftrag*',

            'specification' => 'Spezifikation',
            'field_is_special' => 'Sonderzeit',

            'create' => 'Erstellen',
            'create_and_add_another' => 'Erstellen & weiteren Eintrag hinzufügen',
        ],
        'table' => [
            'date' => 'Datum',
            'description' => 'Beschreibung',
            'total_hours' => 'Gesamtstunden',
            'total_minutes' => 'Gesamtminuten',
            'is_special' => 'Sonderzeit',
        ],
        'filters' => [
            'contract_classification_user' => 'Benutzer',
            'contract_classification_contract' => 'Auftrag',
            'date_from' => 'Von',
            'date_until' => 'Bis',
        ],
        'bulk_actions' => [
            'select_all' => 'Alle Einträge für Stapelverarbeitung auswählen',
            'deselect_all' => 'Alle Einträge für Stapelverarbeitung abwählen',
        ],
    ],
    'bill' => [
        'resource' => [
            'name' => 'Rechnung',
            'group' => 'Aufträge',  // Navigation group label
            'name_plural' => 'Rechnungen',
        ],
        'form' => [
            'field_flat_rate_amount'=>'Pauschalbetrag',
            'field_is_flat_rate_helper'=>"Handelt es sich um einen Pauschalbetrag?",
            'field_is_flat_rate' => 'Pauschalbetrag',
            'section_general' => 'Allgemeine Informationen',
            'field_name' => 'Rechnungsname',
            'field_hourly_rate' => 'Stundensatz',
            'field_description' => 'Beschreibung',
            'field_due_to' => 'Fällig am',
            'field_created_on' => 'Erstellt am',
            'field_is_payed' => 'Bezahlt',

            'section_contract' => 'Auftrag',
            'field_contract' => 'Auftrag',

            'section_attachments' => 'Anhänge',
            'field_attachments' => 'Anhänge',
        ],
        'table' => [
            'user' => 'Benutzer',
            'contract' => 'Auftrag',
            'name' => 'Name',
            'description' => 'Beschreibung',
            'calculated_total' => 'Gesamt (€)',
            'is_payed' => 'Bezahlt',
        ],
        'filters' => [
            'payed' => 'Bezahlt',
            'flat_rate' => 'Pauschalbetrag',

            'user' => [
                'label' => 'Benutzer',
                'placeholder' => 'Benutzer auswählen',
            ],

            'contract' => [
                'label' => 'Auftrag',
                'placeholder' => 'Auftrag auswählen',
            ],
            'due_to' => 'Fälligkeitsdatum',
            'due_from' => 'Fällig ab',
            'due_until' => 'Fällig bis',
            'created_on' => 'Erstellungsdatum',
            'created_from' => 'Erstellt ab',
            'created_until' => 'Erstellt bis',
        ],
    ],
    'credentials' => [
        'resource' => [
            'name' => 'Zugang',
            'group' => 'Allgemein',
            'name_plural' => 'Zugänge',
        ],
        'form' => [
            'section_general' => 'Allgemeine Informationen',
            'field_name' => 'Zugangsname',
            'field_email' => 'E-Mail',
            'field_password' => 'Passwort',
            'field_description' => 'Beschreibung',
            'field_attachments' => 'Anhänge',
        ],
        'table' => [
            'name' => 'Name',
            'email' => 'E-Mail',
            'description' => 'Beschreibung',
            'created_at' => 'Erstellt am',
        ],
        'filters' => [
            'email' => 'E-Mail-Domain',
            'name' => 'Name enthält',
        ],
    ],
    'general_todo' => [
        'resource' => [
            'name' => 'Allgemeines Todos',
            'group' => 'Allgemein',
            'name_plural' => 'Allgemeine Todos',
        ],
        'navigation' => [
            'group' => 'Allgemein',
            'label' => 'Aufgaben',
        ],
        'form' => [
            'name' => 'Name',
            'due_to' => 'Fällig am',
            'description' => 'Beschreibung',
            'is_done' => 'Erledigt',
            'priority' => 'Priorität',
            'attachments' => 'Anhänge',
            'general' => 'Allgemeine Informationen',
            'priority_options' => [
                'low' => 'Niedrig',
                'mid' => 'Mittel',
                'high' => 'Hoch',
            ],
        ],
        'table' => [
            'name' => 'Name',
            'user' => 'Benutzer',
            'due_to' => 'Fällig am',
            'priority' => 'Priorität',
            'is_done' => 'Erledigt',
        ],
        'filters' => [
            'priority' => 'Priorität',
            'is_done' => 'Erledigt-Status',
            'due_to' => 'Fälligkeitszeitraum',
            'due_from' => 'Von',
            'due_until' => 'Bis',
            'user' => 'Benutzer',
        ],
    ],


];
