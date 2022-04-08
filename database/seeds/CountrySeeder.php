<?php

use Illuminate\Database\Seeder;

use App\Models\Country;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $countryList = [
            [
                'status' => 0,  'sort_code' => 'AF', 'flag' => '🇦🇫', 'phone_code' => '+93', 'name' => 'Afghanistan'
            ],
            [
                'status' => 0, 'sort_code' => 'AL', 'flag' => '🇦🇱', 'phone_code' => '+355', 'name' => 'Albania',
            ],
            [
                'status' => 0, 'sort_code' => 'DZ', 'flag' => '🇩🇿', 'phone_code' => '+213', 'name' => 'Algeria',
            ],
            [
                'status' => 0, 'sort_code' => 'AS', 'flag' => '🇦🇸', 'phone_code' => '+1684', 'name' => 'American Samoa',
            ],
            [
                'status' => 0, 'sort_code' => 'AD', 'flag' => '🇦🇩', 'phone_code' => '+376', 'name' => 'Andorra',
            ],
            [
                'status' => 0, 'sort_code' => 'AO', 'flag' => '🇦🇴', 'phone_code' => '+244', 'name' => 'Angola',
            ],
            [
                'status' => 0, 'sort_code' => 'AI', 'flag' => '🇦🇮', 'phone_code' => '+1264', 'name' => 'Anguilla',
            ],
            [
                'status' => 0, 'sort_code' => 'AQ', 'flag' => '🇦🇶', 'phone_code' => '0', 'name' => 'Antarctica',
            ],
            [
                'status' => 0, 'sort_code' => 'AR', 'flag' => '🇦🇷', 'phone_code' => '+54', 'name' => 'Argentina',
            ],
            [
                'status' => 0, 'sort_code' => 'AM', 'flag' => '🇦🇲', 'phone_code' => '+374', 'name' => 'Armenia',
            ],
            [
                'status' => 0, 'sort_code' => 'AW', 'flag' => '🇦🇼', 'phone_code' => '+297', 'name' => 'Aruba',
            ],
            [
                'status' => 0, 'sort_code' => 'AU', 'flag' => '🇦🇺', 'phone_code' => '+61', 'name' => 'Australia',
            ],
            [
                'status' => 0, 'sort_code' => 'AT', 'flag' => '🇦🇹', 'phone_code' => '+43', 'name' => 'Austria',
            ],
            [
                'status' => 0, 'sort_code' => 'AZ', 'flag' => '🇦🇿', 'phone_code' => '+994', 'name' => 'Azerbaijan',
            ],
            [
                'status' => 0, 'sort_code' => 'BH', 'flag' => '🇧🇭', 'phone_code' => '+973', 'name' => 'Bahrain',
            ],
            [
                'status' => 0, 'sort_code' => 'BD', 'flag' => '🇧🇩', 'phone_code' => '+880', 'name' => 'Bangladesh',
            ],
            [
                'status' => 0, 'sort_code' => 'BB', 'flag' => '🇧🇧', 'phone_code' => '+1246', 'name' => 'Barbados',
            ],
            [
                'status' => 0, 'sort_code' => 'BY', 'flag' => '🇧🇾', 'phone_code' => '+375', 'name' => 'Belarus',
            ],
            [
                'status' => 0, 'sort_code' => 'BE', 'flag' => '🇧🇪', 'phone_code' => '+32', 'name' => 'Belgium',
            ],
            [
                'status' => 0, 'sort_code' => 'BZ', 'flag' => '🇧🇿', 'phone_code' => '+501', 'name' => 'Belize',
            ],
            [
                'status' => 0, 'sort_code' => 'BJ', 'flag' => '🇧🇯', 'phone_code' => '+229', 'name' => 'Benin',
            ],
            [
                'status' => 0, 'sort_code' => 'BM', 'flag' => '🇧🇲', 'phone_code' => '+1441', 'name' => 'Bermuda',
            ],
            [
                'status' => 0, 'sort_code' => 'BT', 'flag' => '🇧🇹', 'phone_code' => '+975', 'name' => 'Bhutan',
            ],
            [
                'status' => 0, 'sort_code' => 'BO', 'flag' => '🇧🇴', 'phone_code' => '+591', 'name' => 'Bolivia',
            ],
            [
                'status' => 0, 'sort_code' => 'BW', 'flag' => '🇧🇼', 'phone_code' => '+267', 'name' => 'Botswana',
            ],
            [
                'status' => 0, 'sort_code' => 'BV', 'flag' => '🇧🇻', 'phone_code' => '0', 'name' => 'Bouvet Island',
            ],
            [
                'status' => 0, 'sort_code' => 'BR', 'flag' => '🇧🇷', 'phone_code' => '+55', 'name' => 'Brazil',
            ],
            [
                'status' => 0, 'sort_code' => 'IO', 'flag' => '🇮🇴', 'phone_code' => '+246', 'name' => 'British Indian Ocean Territory',
            ],
            [
                'status' => 0, 'sort_code' => 'BN', 'flag' => '🇧🇳', 'phone_code' => '+673', 'name' => 'Brunei',
            ],
            [
                'status' => 0, 'sort_code' => 'BG', 'flag' => '🇧🇬', 'phone_code' => '+359', 'name' => 'Bulgaria',
            ],
            [
                'status' => 0, 'sort_code' => 'BF', 'flag' => '🇧🇫', 'phone_code' => '+226', 'name' => 'Burkina Faso',
            ],
            [
                'status' => 0, 'sort_code' => 'BI', 'flag' => '🇧🇮', 'phone_code' => '+257', 'name' => 'Burundi',
            ],
            [
                'status' => 0, 'sort_code' => 'KH', 'flag' => '🇰🇭', 'phone_code' => '+855', 'name' => 'Cambodia',
            ],
            [
                'status' => 0, 'sort_code' => 'CM', 'flag' => '🇨🇲', 'phone_code' => '+237', 'name' => 'Cameroon',
            ],
            [
                'status' => 0, 'sort_code' => 'CA', 'flag' => '🇨🇦', 'phone_code' => '+1', 'name' => 'Canada',
            ],
            [
                'status' => 0, 'sort_code' => 'CV', 'flag' => '🇨🇻', 'phone_code' => '+238', 'name' => 'Cape Verde',
            ],
            [
                'status' => 0, 'sort_code' => 'KY', 'flag' => '🇰🇾', 'phone_code' => '+1345', 'name' => 'Cayman Islands',
            ],
            [
                'status' => 0, 'sort_code' => 'CF', 'flag' => '🇨🇫', 'phone_code' => '+236', 'name' => 'Central African Republic',
            ],
            [
                'status' => 0, 'sort_code' => 'TD', 'flag' => '🇹🇩', 'phone_code' => '+235', 'name' => 'Chad',
            ],
            [
                'status' => 0, 'sort_code' => 'CL', 'flag' => '🇨🇱', 'phone_code' => '+56', 'name' => 'Chile',
            ],
            [
                'status' => 0, 'sort_code' => 'CN', 'flag' => '🇨🇳', 'phone_code' => '+86', 'name' => 'China',
            ],
            [
                'status' => 0, 'sort_code' => 'CX', 'flag' => '🇨🇽', 'phone_code' => '+61', 'name' => 'Christmas Island',
            ],
            [
                'status' => 0, 'sort_code' => 'CC', 'flag' => '🇨🇨', 'phone_code' => '+672', 'name' => 'Cocos (Keeling) Islands',
            ],
            [
                'status' => 0, 'sort_code' => 'CO', 'flag' => '🇨🇴', 'phone_code' => '+57', 'name' => 'Colombia',
            ],
            [
                'status' => 0, 'sort_code' => 'KM', 'flag' => '🇰🇲', 'phone_code' => '+269', 'name' => 'Comoros',
            ],
            [
                'status' => 0, 'sort_code' => 'CK', 'flag' => '🇨🇰', 'phone_code' => '+682', 'name' => 'Cook Islands',
            ],
            [
                'status' => 0, 'sort_code' => 'CR', 'flag' => '🇨🇷', 'phone_code' => '+506', 'name' => 'Costa Rica',
            ],
            [
                'status' => 0, 'sort_code' => 'CU', 'flag' => '🇨🇺', 'phone_code' => '+53', 'name' => 'Cuba',
            ],
            [
                'status' => 0, 'sort_code' => 'CY', 'flag' => '🇨🇾', 'phone_code' => '+357', 'name' => 'Cyprus',
            ],
            [
                'status' => 0, 'sort_code' => 'DK', 'flag' => '🇩🇰', 'phone_code' => '+45', 'name' => 'Denmark',
            ],
            [
                'status' => 0, 'sort_code' => 'DJ', 'flag' => '🇩🇯', 'phone_code' => '+253', 'name' => 'Djibouti',
            ],
            [
                'status' => 0, 'sort_code' => 'DM', 'flag' => '🇩🇲', 'phone_code' => '+1767', 'name' => 'Dominica',
            ],
            [
                'status' => 0, 'sort_code' => 'DO', 'flag' => '🇩🇴', 'phone_code' => '+1809', 'name' => 'Dominican Republic',
            ],
            [
                'status' => 0, 'sort_code' => 'EC', 'flag' => '🇪🇨', 'phone_code' => '+593', 'name' => 'Ecuador',
            ],
            [
                'status' => 0, 'sort_code' => 'EG', 'flag' => '🇪🇬', 'phone_code' => '+20', 'name' => 'Egypt',
            ],
            [
                'status' => 0, 'sort_code' => 'SV', 'flag' => '🇸🇻', 'phone_code' => '+503', 'name' => 'El Salvador',
            ],
            [
                'status' => 0, 'sort_code' => 'GQ', 'flag' => '🇬🇶', 'phone_code' => '+240', 'name' => 'Equatorial Guinea',
            ],
            [
                'status' => 0, 'sort_code' => 'ER', 'flag' => '🇪🇷', 'phone_code' => '+291', 'name' => 'Eritrea',
            ],
            [
                'status' => 0, 'sort_code' => 'EE', 'flag' => '🇪🇪', 'phone_code' => '+372', 'name' => 'Estonia',
            ],
            [
                'status' => 0, 'sort_code' => 'ET', 'flag' => '🇪🇹', 'phone_code' => '+251', 'name' => 'Ethiopia',
            ],
            [
                'status' => 0, 'sort_code' => 'FK', 'flag' => '🇫🇰', 'phone_code' => '+500', 'name' => 'Falkland Islands',
            ],
            [
                'status' => 0, 'sort_code' => 'FO', 'flag' => '🇫🇴', 'phone_code' => '+298', 'name' => 'Faroe Islands',
            ],
            [
                'status' => 0, 'sort_code' => 'FI', 'flag' => '🇫🇮', 'phone_code' => '+358', 'name' => 'Finland',
            ],
            [
                'status' => 0, 'sort_code' => 'FR', 'flag' => '🇫🇷', 'phone_code' => '+33', 'name' => 'France',
            ],
            [
                'status' => 0, 'sort_code' => 'GF', 'flag' => '🇬🇫', 'phone_code' => '+594', 'name' => 'French Guiana',
            ],
            [
                'status' => 0, 'sort_code' => 'PF', 'flag' => '🇵🇫', 'phone_code' => '+689', 'name' => 'French Polynesia',
            ],
            [
                'status' => 0, 'sort_code' => 'TF', 'flag' => '🇹🇫', 'phone_code' => '0', 'name' => 'French Southern Territories',
            ],
            [
                'status' => 0, 'sort_code' => 'GA', 'flag' => '🇬🇦', 'phone_code' => '+241', 'name' => 'Gabon',
            ],
            [
                'status' => 0, 'sort_code' => 'GE', 'flag' => '🇬🇪', 'phone_code' => '+995', 'name' => 'Georgia',
            ],
            [
                'status' => 0, 'sort_code' => 'DE', 'flag' => '🇩🇪', 'phone_code' => '+49', 'name' => 'Germany',
            ],
            [
                'status' => 0, 'sort_code' => 'GH', 'flag' => '🇬🇭', 'phone_code' => '+233', 'name' => 'Ghana',
            ],
            [
                'status' => 0, 'sort_code' => 'GI', 'flag' => '🇬🇮', 'phone_code' => '+350', 'name' => 'Gibraltar',
            ],
            [
                'status' => 0, 'sort_code' => 'GR', 'flag' => '🇬🇷', 'phone_code' => '+30', 'name' => 'Greece',
            ],
            [
                'status' => 0, 'sort_code' => 'GL', 'flag' => '🇬🇱', 'phone_code' => '+299', 'name' => 'Greenland',
            ],
            [
                'status' => 0, 'sort_code' => 'GD', 'flag' => '🇬🇩', 'phone_code' => '+1473', 'name' => 'Grenada',
            ],
            [
                'status' => 0, 'sort_code' => 'GP', 'flag' => '🇬🇵', 'phone_code' => '+590', 'name' => 'Guadeloupe',
            ],
            [
                'status' => 0, 'sort_code' => 'GU', 'flag' => '🇬🇺', 'phone_code' => '+1671', 'name' => 'Guam',
            ],
            [
                'status' => 0, 'sort_code' => 'GT', 'flag' => '🇬🇹', 'phone_code' => '+502', 'name' => 'Guatemala',
            ],
            [
                'status' => 0, 'sort_code' => 'GN', 'flag' => '🇬🇳', 'phone_code' => '+224', 'name' => 'Guinea',
            ],
            [
                'status' => 0, 'sort_code' => 'GW', 'flag' => '🇬🇼', 'phone_code' => '+245', 'name' => 'Guinea-Bissau',
            ],
            [
                'status' => 0, 'sort_code' => 'GY', 'flag' => '🇬🇾', 'phone_code' => '+592', 'name' => 'Guyana',
            ],
            [
                'status' => 0, 'sort_code' => 'HT', 'flag' => '🇭🇹', 'phone_code' => '+509', 'name' => 'Haiti',
            ],
            [
                'status' => 0, 'sort_code' => 'HN', 'flag' => '🇭🇳', 'phone_code' => '+504', 'name' => 'Honduras',
            ],
            [
                'status' => 0, 'sort_code' => 'HU', 'flag' => '🇭🇺', 'phone_code' => '+36', 'name' => 'Hungary',
            ],
            [
                'status' => 0, 'sort_code' => 'IS', 'flag' => '🇮🇸', 'phone_code' => '+354', 'name' => 'Iceland',
            ],
            [
                'status' => 1, 'sort_code' => 'IN', 'flag' => '🇮🇳', 'phone_code' => '+91', 'name' => 'India',
            ],
            [
                'status' => 0, 'sort_code' => 'ID', 'flag' => '🇮🇩', 'phone_code' => '+62', 'name' => 'Indonesia',
            ],
            [
                'status' => 0, 'sort_code' => 'IR', 'flag' => '🇮🇷', 'phone_code' => '+98', 'name' => 'Iran',
            ],
            [
                'status' => 0, 'sort_code' => 'IQ', 'flag' => '🇮🇶', 'phone_code' => '+964', 'name' => 'Iraq',
            ],
            [
                'status' => 0, 'sort_code' => 'IE', 'flag' => '🇮🇪', 'phone_code' => '+353', 'name' => 'Ireland',
            ],
            [
                'status' => 0, 'sort_code' => 'IL', 'flag' => '🇮🇱', 'phone_code' => '+972', 'name' => 'Israel',
            ],
            [
                'status' => 0, 'sort_code' => 'IT', 'flag' => '🇮🇹', 'phone_code' => '+39', 'name' => 'Italy',
            ],
            [
                'status' => 0, 'sort_code' => 'JM', 'flag' => '🇯🇲', 'phone_code' => '+1876', 'name' => 'Jamaica',
            ],
            [
                'status' => 0, 'sort_code' => 'JP', 'flag' => '🇯🇵', 'phone_code' => '+81', 'name' => 'Japan',
            ],
            [
                'status' => 0, 'sort_code' => 'JO', 'flag' => '🇯🇴', 'phone_code' => '+962', 'name' => 'Jordan',
            ],
            [
                'status' => 0, 'sort_code' => 'KZ', 'flag' => '🇰🇿', 'phone_code' => '+7', 'name' => 'Kazakhstan',
            ],
            [
                'status' => 0, 'sort_code' => 'KE', 'flag' => '🇰🇪', 'phone_code' => '+254', 'name' => 'Kenya',
            ],
            [
                'status' => 0, 'sort_code' => 'KI', 'flag' => '🇰🇮', 'phone_code' => '+686', 'name' => 'Kiribati',
            ],
            [
                'status' => 0, 'sort_code' => 'KW', 'flag' => '🇰🇼', 'phone_code' => '+965', 'name' => 'Kuwait',
            ],
            [
                'status' => 0, 'sort_code' => 'KG', 'flag' => '🇰🇬', 'phone_code' => '+996', 'name' => 'Kyrgyzstan',
            ],
            [
                'status' => 0, 'sort_code' => 'LA', 'flag' => '🇱🇦', 'phone_code' => '+856', 'name' => 'Laos',
            ],
            [
                'status' => 0, 'sort_code' => 'LV', 'flag' => '🇱🇻', 'phone_code' => '+371', 'name' => 'Latvia',
            ],
            [
                'status' => 0, 'sort_code' => 'LB', 'flag' => '🇱🇧', 'phone_code' => '+961', 'name' => 'Lebanon',
            ],
            [
                'status' => 0, 'sort_code' => 'LS', 'flag' => '🇱🇸', 'phone_code' => '+266', 'name' => 'Lesotho',
            ],
            [
                'status' => 0, 'sort_code' => 'LR', 'flag' => '🇱🇷', 'phone_code' => '+231', 'name' => 'Liberia',
            ],
            [
                'status' => 0, 'sort_code' => 'LY', 'flag' => '🇱🇾', 'phone_code' => '+218', 'name' => 'Libya',
            ],
            [
                'status' => 0, 'sort_code' => 'LI', 'flag' => '🇱🇮', 'phone_code' => '+423', 'name' => 'Liechtenstein',
            ],
            [
                'status' => 0, 'sort_code' => 'LT', 'flag' => '🇱🇹', 'phone_code' => '+370', 'name' => 'Lithuania',
            ],
            [
                'status' => 0, 'sort_code' => 'LU', 'flag' => '🇱🇺', 'phone_code' => '+352', 'name' => 'Luxembourg',
            ],
            [
                'status' => 0, 'sort_code' => 'MK', 'flag' => '🇲🇰', 'phone_code' => '+389', 'name' => 'Macedonia',
            ],
            [
                'status' => 0, 'sort_code' => 'MG', 'flag' => '🇲🇬', 'phone_code' => '+261', 'name' => 'Madagascar',
            ],
            [
                'status' => 0, 'sort_code' => 'MW', 'flag' => '🇲🇼', 'phone_code' => '+265', 'name' => 'Malawi',
            ],
            [
                'status' => 0, 'sort_code' => 'MY', 'flag' => '🇲🇾', 'phone_code' => '+60', 'name' => 'Malaysia',
            ],
            [
                'status' => 0, 'sort_code' => 'MV', 'flag' => '🇲🇻', 'phone_code' => '+960', 'name' => 'Maldives',
            ],
            [
                'status' => 0, 'sort_code' => 'ML', 'flag' => '🇲🇱', 'phone_code' => '+223', 'name' => 'Mali',
            ],
            [
                'status' => 0, 'sort_code' => 'MT', 'flag' => '🇲🇹', 'phone_code' => '+356', 'name' => 'Malta',
            ],
            [
                'status' => 0, 'sort_code' => 'MH', 'flag' => '🇲🇭', 'phone_code' => '+692', 'name' => 'Marshall Islands',
            ],
            [
                'status' => 0, 'sort_code' => 'MQ', 'flag' => '🇲🇶', 'phone_code' => '+596', 'name' => 'Martinique',
            ],
            [
                'status' => 0, 'sort_code' => 'MR', 'flag' => '🇲🇷', 'phone_code' => '+222', 'name' => 'Mauritania',
            ],
            [
                'status' => 0, 'sort_code' => 'MU', 'flag' => '🇲🇺', 'phone_code' => '+230', 'name' => 'Mauritius',
            ],
            [
                'status' => 0, 'sort_code' => 'YT', 'flag' => '🇾🇹', 'phone_code' => '+269', 'name' => 'Mayotte',
            ],
            [
                'status' => 0, 'sort_code' => 'MX', 'flag' => '🇲🇽', 'phone_code' => '+52', 'name' => 'Mexico',
            ],
            [
                'status' => 0, 'sort_code' => 'FM', 'flag' => '🇫🇲', 'phone_code' => '+691', 'name' => 'Micronesia',
            ],
            [
                'status' => 0, 'sort_code' => 'MD', 'flag' => '🇲🇩', 'phone_code' => '+373', 'name' => 'Moldova',
            ],
            [
                'status' => 0, 'sort_code' => 'MC', 'flag' => '🇲🇨', 'phone_code' => '+377', 'name' => 'Monaco',
            ],
            [
                'status' => 0, 'sort_code' => 'MN', 'flag' => '🇲🇳', 'phone_code' => '+976', 'name' => 'Mongolia',
            ],
            [
                'status' => 0, 'sort_code' => 'MS', 'flag' => '🇲🇸', 'phone_code' => '+1664', 'name' => 'Montserrat',
            ],
            [
                'status' => 0, 'sort_code' => 'MA', 'flag' => '🇲🇦', 'phone_code' => '+212', 'name' => 'Morocco',
            ],
            [
                'status' => 0, 'sort_code' => 'MZ', 'flag' => '🇲🇿', 'phone_code' => '+258', 'name' => 'Mozambique',
            ],
            [
                'status' => 0, 'sort_code' => 'NA', 'flag' => '🇳🇦', 'phone_code' => '+264', 'name' => 'Namibia',
            ],
            [
                'status' => 0, 'sort_code' => 'NR', 'flag' => '🇳🇷', 'phone_code' => '+674', 'name' => 'Nauru',
            ],
            [
                'status' => 0, 'sort_code' => 'NP', 'flag' => '🇳🇵', 'phone_code' => '+977', 'name' => 'Nepal',
            ],
            [
                'status' => 0, 'sort_code' => 'NC', 'flag' => '🇳🇨', 'phone_code' => '+687', 'name' => 'New Caledonia',
            ],
            [
                'status' => 0, 'sort_code' => 'NZ', 'flag' => '🇳🇿', 'phone_code' => '+64', 'name' => 'New Zealand',
            ],
            [
                'status' => 0, 'sort_code' => 'NI', 'flag' => '🇳🇮', 'phone_code' => '+505', 'name' => 'Nicaragua',
            ],
            [
                'status' => 0, 'sort_code' => 'NE', 'flag' => '🇳🇪', 'phone_code' => '+227', 'name' => 'Niger',
            ],
            [
                'status' => 0, 'sort_code' => 'NG', 'flag' => '🇳🇬', 'phone_code' => '+234', 'name' => 'Nigeria',
            ],
            [
                'status' => 0, 'sort_code' => 'NU', 'flag' => '🇳🇺', 'phone_code' => '+683', 'name' => 'Niue',
            ],
            [
                'status' => 0, 'sort_code' => 'NF', 'flag' => '🇳🇫', 'phone_code' => '+672', 'name' => 'Norfolk Island',
            ],
            [
                'status' => 0, 'sort_code' => 'MP', 'flag' => '🇲🇵', 'phone_code' => '+1670', 'name' => 'Northern Mariana Islands',
            ],
            [
                'status' => 0, 'sort_code' => 'NO', 'flag' => '🇳🇴', 'phone_code' => '+47', 'name' => 'Norway',
            ],
            [
                'status' => 0, 'sort_code' => 'OM', 'flag' => '🇴🇲', 'phone_code' => '+968', 'name' => 'Oman',
            ],
            [
                'status' => 0, 'sort_code' => 'PK', 'flag' => '🇵🇰', 'phone_code' => '+92', 'name' => 'Pakistan',
            ],
            [
                'status' => 0, 'sort_code' => 'PW', 'flag' => '🇵🇼', 'phone_code' => '+680', 'name' => 'Palau',
            ],
            [
                'status' => 0, 'sort_code' => 'PA', 'flag' => '🇵🇦', 'phone_code' => '+507', 'name' => 'Panama',
            ],
            [
                'status' => 0, 'sort_code' => 'PY', 'flag' => '🇵🇾', 'phone_code' => '+595', 'name' => 'Paraguay',
            ],
            [
                'status' => 0, 'sort_code' => 'PE', 'flag' => '🇵🇪', 'phone_code' => '+51', 'name' => 'Peru',
            ],
            [
                'status' => 0, 'sort_code' => 'PH', 'flag' => '🇵🇭', 'phone_code' => '+63', 'name' => 'Philippines',
            ],
            [
                'status' => 0, 'sort_code' => 'PL', 'flag' => '🇵🇱', 'phone_code' => '+48', 'name' => 'Poland',
            ],
            [
                'status' => 0, 'sort_code' => 'PT', 'flag' => '🇵🇹', 'phone_code' => '+351', 'name' => 'Portugal',
            ],
            [
                'status' => 0, 'sort_code' => 'PR', 'flag' => '🇵🇷', 'phone_code' => '+1787', 'name' => 'Puerto Rico',
            ],
            [
                'status' => 0, 'sort_code' => 'QA', 'flag' => '🇶🇦', 'phone_code' => '+974', 'name' => 'Qatar',
            ],
            [
                'status' => 0, 'sort_code' => 'RO', 'flag' => '🇷🇴', 'phone_code' => '+40', 'name' => 'Romania',
            ],
            [
                'status' => 0, 'sort_code' => 'RU', 'flag' => '🇷🇺', 'phone_code' => '+70', 'name' => 'Russia',
            ],
            [
                'status' => 0, 'sort_code' => 'RW', 'flag' => '🇷🇼', 'phone_code' => '+250', 'name' => 'Rwanda',
            ],
            [
                'status' => 0, 'sort_code' => 'WS', 'flag' => '🇼🇸', 'phone_code' => '+684', 'name' => 'Samoa',
            ],
            [
                'status' => 0, 'sort_code' => 'SM', 'flag' => '🇸🇲', 'phone_code' => '+378', 'name' => 'San Marino',
            ],
            [
                'status' => 0, 'sort_code' => 'SA', 'flag' => '🇸🇦', 'phone_code' => '+966', 'name' => 'Saudi Arabia',
            ],
            [
                'status' => 0, 'sort_code' => 'SN', 'flag' => '🇸🇳', 'phone_code' => '+221', 'name' => 'Senegal',
            ],
            [
                'status' => 0, 'sort_code' => 'RS', 'flag' => '🇷🇸', 'phone_code' => '+381', 'name' => 'Serbia',
            ],
            [
                'status' => 0, 'sort_code' => 'SC', 'flag' => '🇸🇨', 'phone_code' => '+248', 'name' => 'Seychelles',
            ],
            [
                'status' => 0, 'sort_code' => 'SL', 'flag' => '🇸🇱', 'phone_code' => '+232', 'name' => 'Sierra Leone',
            ],
            [
                'status' => 0, 'sort_code' => 'SG', 'flag' => '🇸🇬', 'phone_code' => '+65', 'name' => 'Singapore',
            ],
            [
                'status' => 0, 'sort_code' => 'SK', 'flag' => '🇸🇰', 'phone_code' => '+421', 'name' => 'Slovakia',
            ],
            [
                'status' => 0, 'sort_code' => 'SI', 'flag' => '🇸🇮', 'phone_code' => '+386', 'name' => 'Slovenia',
            ],
            [
                'status' => 0, 'sort_code' => 'SB', 'flag' => '🇸🇧', 'phone_code' => '+677', 'name' => 'Solomon Islands',
            ],
            [
                'status' => 0, 'sort_code' => 'SO', 'flag' => '🇸🇴', 'phone_code' => '+252', 'name' => 'Somalia',
            ],
            [
                'status' => 0, 'sort_code' => 'ZA', 'flag' => '🇿🇦', 'phone_code' => '+27', 'name' => 'South Africa',
            ],
            [
                'status' => 0, 'sort_code' => 'SS', 'flag' => '🇸🇸', 'phone_code' => '+211', 'name' => 'South Sudan',
            ],
            [
                'status' => 0, 'sort_code' => 'ES', 'flag' => '🇪🇸', 'phone_code' => '+34', 'name' => 'Spain',
            ],
            [
                'status' => 0, 'sort_code' => 'LK', 'flag' => '🇱🇰', 'phone_code' => '+94', 'name' => 'Sri Lanka',
            ],
            [
                'status' => 0, 'sort_code' => 'SD', 'flag' => '🇸🇩', 'phone_code' => '+249', 'name' => 'Sudan',
            ],
            [
                'status' => 0, 'sort_code' => 'SR', 'flag' => '🇸🇷', 'phone_code' => '+597', 'name' => 'Suriname',
            ],
            [
                'status' => 0, 'sort_code' => 'SZ', 'flag' => '🇸🇿', 'phone_code' => '+268', 'name' => 'Swaziland',
            ],
            [
                'status' => 0, 'sort_code' => 'SE', 'flag' => '🇸🇪', 'phone_code' => '+46', 'name' => 'Sweden',
            ],
            [
                'status' => 0, 'sort_code' => 'CH', 'flag' => '🇨🇭', 'phone_code' => '+41', 'name' => 'Switzerland',
            ],
            [
                'status' => 0, 'sort_code' => 'SY', 'flag' => '🇸🇾', 'phone_code' => '+963', 'name' => 'Syria',
            ],
            [
                'status' => 0, 'sort_code' => 'TW', 'flag' => '🇹🇼', 'phone_code' => '+886', 'name' => 'Taiwan',
            ],
            [
                'status' => 0, 'sort_code' => 'TJ', 'flag' => '🇹🇯', 'phone_code' => '+992', 'name' => 'Tajikistan',
            ],
            [
                'status' => 0, 'sort_code' => 'TZ', 'flag' => '🇹🇿', 'phone_code' => '+255', 'name' => 'Tanzania',
            ],
            [
                'status' => 0, 'sort_code' => 'TH', 'flag' => '🇹🇭', 'phone_code' => '+66', 'name' => 'Thailand',
            ],
            [
                'status' => 0, 'sort_code' => 'TG', 'flag' => '🇹🇬', 'phone_code' => '+228', 'name' => 'Togo',
            ],
            [
                'status' => 0, 'sort_code' => 'TK', 'flag' => '🇹🇰', 'phone_code' => '+690', 'name' => 'Tokelau',
            ],
            [
                'status' => 0, 'sort_code' => 'TO', 'flag' => '🇹🇴', 'phone_code' => '+676', 'name' => 'Tonga',
            ],
            [
                'status' => 0, 'sort_code' => 'TN', 'flag' => '🇹🇳', 'phone_code' => '+216', 'name' => 'Tunisia',
            ],
            [
                'status' => 0, 'sort_code' => 'TR', 'flag' => '🇹🇷', 'phone_code' => '+90', 'name' => 'Turkey',
            ],
            [
                'status' => 0, 'sort_code' => 'TM', 'flag' => '🇹🇲', 'phone_code' => '+7370', 'name' => 'Turkmenistan',
            ],
            [
                'status' => 0, 'sort_code' => 'TV', 'flag' => '🇹🇻', 'phone_code' => '+688', 'name' => 'Tuvalu',
            ],
            [
                'status' => 0, 'sort_code' => 'UG', 'flag' => '🇺🇬', 'phone_code' => '+256', 'name' => 'Uganda',
            ],
            [
                'status' => 0, 'sort_code' => 'UA', 'flag' => '🇺🇦', 'phone_code' => '+380', 'name' => 'Ukraine',
            ],
            [
                'status' => 0, 'sort_code' => 'AE', 'flag' => '🇦🇪', 'phone_code' => '+971', 'name' => 'United Arab Emirates',
            ],
            [
                'status' => 0, 'sort_code' => 'GB', 'flag' => '🇬🇧', 'phone_code' => '+44', 'name' => 'United Kingdom',
            ],
            [
                'status' => 1, 'sort_code' => 'US', 'flag' => '🇺🇸', 'phone_code' => '+1', 'name' => 'United States',
            ],
            [
                'status' => 0, 'sort_code' => 'UY', 'flag' => '🇺🇾', 'phone_code' => '+598', 'name' => 'Uruguay',
            ],
            [
                'status' => 0, 'sort_code' => 'UZ', 'flag' => '🇺🇿', 'phone_code' => '+998', 'name' => 'Uzbekistan',
            ],
            [
                'status' => 0, 'sort_code' => 'VU', 'flag' => '🇻🇺', 'phone_code' => '+678', 'name' => 'Vanuatu',
            ],
            [
                'status' => 0, 'sort_code' => 'VE', 'flag' => '🇻🇪', 'phone_code' => '+58', 'name' => 'Venezuela',
            ],
            [
                'status' => 0, 'sort_code' => 'VN', 'flag' => '🇻🇳', 'phone_code' => '+84', 'name' => 'Vietnam',
            ],
            [
                'status' => 0, 'sort_code' => 'EH', 'flag' => '🇪🇭', 'phone_code' => '+212', 'name' => 'Western Sahara',
            ],
            [
                'status' => 0, 'sort_code' => 'YE', 'flag' => '🇾🇪', 'phone_code' => '+967', 'name' => 'Yemen',
            ],
            [
                'status' => 0, 'sort_code' => 'ZM', 'flag' => '🇿🇲', 'phone_code' => '+260', 'name' => 'Zambia',
            ],
            [
                'status' => 0, 'sort_code' => 'ZW', 'flag' => '🇿🇼', 'phone_code' => '+26', 'name' => 'Zimbabwe'
            ]
        ];
        Country::truncate();
        Country::insert($countryList);
    }
}
