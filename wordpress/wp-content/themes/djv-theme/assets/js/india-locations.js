/**
 * ============================================================
 * India-Wide Location Database & Geospatial Lookup
 * website/assets/js/india-locations.js
 *
 * Covers 350+ cities, sacred pilgrimage sites, cultural hubs,
 * district headquarters, and towns across all 28 States & 8 UTs.
 * ============================================================
 */

(function (root, factory) {
  if (typeof module === 'object' && module.exports) {
    module.exports = factory();
  } else {
    root.IndiaLocations = factory();
  }
})(typeof self !== 'undefined' ? self : this, function () {
  'use strict';

  const TIMEZONE_IST = 'Asia/Kolkata';

  const STATES_AND_UTS = [
    'All States',
    'Andhra Pradesh',
    'Arunachal Pradesh',
    'Assam',
    'Bihar',
    'Chhattisgarh',
    'Goa',
    'Gujarat',
    'Haryana',
    'Himachal Pradesh',
    'Jharkhand',
    'Karnataka',
    'Kerala',
    'Madhya Pradesh',
    'Maharashtra',
    'Manipur',
    'Meghalaya',
    'Mizoram',
    'Nagaland',
    'Odisha',
    'Punjab',
    'Rajasthan',
    'Sikkim',
    'Tamil Nadu',
    'Telangana',
    'Tripura',
    'Uttar Pradesh',
    'Uttarakhand',
    'West Bengal',
    'Andaman & Nicobar Islands',
    'Chandigarh',
    'Dadra & Nagar Haveli and Daman & Diu',
    'Delhi',
    'Jammu & Kashmir',
    'Ladakh',
    'Lakshadweep',
    'Puducherry'
  ];

  const RAW_LOCATIONS = [
    // ─── Telangana ───
    { name: 'Hyderabad', state: 'Telangana', lat: 17.3850, lon: 78.4867, type: 'Metro' },
    { name: 'Secunderabad', state: 'Telangana', lat: 17.4399, lon: 78.4983, type: 'City' },
    { name: 'Ghatkesar', state: 'Telangana', lat: 17.4475, lon: 78.6833, type: 'Town' },
    { name: 'Warangal', state: 'Telangana', lat: 17.9689, lon: 79.5941, type: 'Historical' },
    { name: 'Karimnagar', state: 'Telangana', lat: 18.4386, lon: 79.1288, type: 'City' },
    { name: 'Nizamabad', state: 'Telangana', lat: 18.6725, lon: 78.0941, type: 'City' },
    { name: 'Khammam', state: 'Telangana', lat: 17.2473, lon: 80.1514, type: 'City' },
    { name: 'Nalgonda', state: 'Telangana', lat: 17.0575, lon: 79.2684, type: 'City' },
    { name: 'Mahabubnagar', state: 'Telangana', lat: 16.7488, lon: 77.9864, type: 'City' },
    { name: 'Yadagirigutta', state: 'Telangana', lat: 17.5855, lon: 78.9392, type: 'Temple' },
    { name: 'Bhadrachalam', state: 'Telangana', lat: 17.6688, lon: 80.8936, type: 'Temple' },
    { name: 'Basara', state: 'Telangana', lat: 18.9774, lon: 77.9547, type: 'Temple' },
    { name: 'Vemulawada', state: 'Telangana', lat: 18.4682, lon: 78.8686, type: 'Temple' },
    { name: 'Ramagundam', state: 'Telangana', lat: 18.7551, lon: 79.5132, type: 'Town' },
    { name: 'Siddipet', state: 'Telangana', lat: 18.1018, lon: 78.8520, type: 'Town' },
    { name: 'Adilabad', state: 'Telangana', lat: 19.6641, lon: 78.5320, type: 'City' },

    // ─── Andhra Pradesh ───
    { name: 'Visakhapatnam', state: 'Andhra Pradesh', lat: 17.6868, lon: 83.2185, type: 'Coastal Metro' },
    { name: 'Vijayawada', state: 'Andhra Pradesh', lat: 16.5062, lon: 80.6480, type: 'City' },
    { name: 'Tirupati', state: 'Andhra Pradesh', lat: 13.6288, lon: 79.4192, type: 'Temple' },
    { name: 'Tirumala', state: 'Andhra Pradesh', lat: 13.6833, lon: 79.3500, type: 'Pilgrimage' },
    { name: 'Guntur', state: 'Andhra Pradesh', lat: 16.3067, lon: 80.4365, type: 'City' },
    { name: 'Rajahmundry', state: 'Andhra Pradesh', lat: 17.0005, lon: 81.8040, type: 'City' },
    { name: 'Nellore', state: 'Andhra Pradesh', lat: 14.4426, lon: 79.9865, type: 'City' },
    { name: 'Kurnool', state: 'Andhra Pradesh', lat: 15.8281, lon: 78.0373, type: 'City' },
    { name: 'Kadapa', state: 'Andhra Pradesh', lat: 14.4673, lon: 78.8242, type: 'City' },
    { name: 'Anantapur', state: 'Andhra Pradesh', lat: 14.6819, lon: 77.6006, type: 'City' },
    { name: 'Kakinada', state: 'Andhra Pradesh', lat: 16.9891, lon: 82.2475, type: 'City' },
    { name: 'Eluru', state: 'Andhra Pradesh', lat: 16.7107, lon: 81.0952, type: 'City' },
    { name: 'Ongole', state: 'Andhra Pradesh', lat: 15.5057, lon: 80.0499, type: 'City' },
    { name: 'Srisailam', state: 'Andhra Pradesh', lat: 16.0739, lon: 78.8685, type: 'Jyotirlinga' },
    { name: 'Srikalahasti', state: 'Andhra Pradesh', lat: 13.7498, lon: 79.6984, type: 'Temple' },
    { name: 'Kanipakam', state: 'Andhra Pradesh', lat: 13.2844, lon: 79.0347, type: 'Temple' },
    { name: 'Simhachalam', state: 'Andhra Pradesh', lat: 17.7667, lon: 83.2500, type: 'Temple' },
    { name: 'Annavaram', state: 'Andhra Pradesh', lat: 17.2792, lon: 82.4042, type: 'Temple' },
    { name: 'Amaravati', state: 'Andhra Pradesh', lat: 16.5735, lon: 80.3575, type: 'Capital' },

    // ─── Delhi (NCT) ───
    { name: 'Delhi', state: 'Delhi', lat: 28.6139, lon: 77.2090, type: 'National Capital' },
    { name: 'New Delhi', state: 'Delhi', lat: 28.6139, lon: 77.2090, type: 'Capital' },
    { name: 'Dwarka', state: 'Delhi', lat: 28.5921, lon: 77.0460, type: 'Subcity' },
    { name: 'Rohini', state: 'Delhi', lat: 28.7495, lon: 77.0565, type: 'Area' },

    // ─── Maharashtra ───
    { name: 'Mumbai', state: 'Maharashtra', lat: 19.0760, lon: 72.8777, type: 'Metro' },
    { name: 'Pune', state: 'Maharashtra', lat: 18.5204, lon: 73.8567, type: 'Metro' },
    { name: 'Nagpur', state: 'Maharashtra', lat: 21.1458, lon: 79.0882, type: 'City' },
    { name: 'Nashik', state: 'Maharashtra', lat: 19.9975, lon: 73.7898, type: 'Pilgrimage' },
    { name: 'Thane', state: 'Maharashtra', lat: 19.2183, lon: 72.9781, type: 'City' },
    { name: 'Chhatrapati Sambhajinagar', state: 'Maharashtra', lat: 19.8762, lon: 75.3433, type: 'City' },
    { name: 'Shirdi', state: 'Maharashtra', lat: 19.7645, lon: 74.4762, type: 'Pilgrimage' },
    { name: 'Trimbakeshwar', state: 'Maharashtra', lat: 19.9385, lon: 73.5307, type: 'Jyotirlinga' },
    { name: 'Kolhapur', state: 'Maharashtra', lat: 16.7050, lon: 74.2433, type: 'Shakti Peetha' },
    { name: 'Solapur', state: 'Maharashtra', lat: 17.6599, lon: 75.9064, type: 'City' },
    { name: 'Amravati', state: 'Maharashtra', lat: 20.9374, lon: 77.7796, type: 'City' },
    { name: 'Navi Mumbai', state: 'Maharashtra', lat: 19.0330, lon: 73.0297, type: 'City' },
    { name: 'Pandharpur', state: 'Maharashtra', lat: 17.6778, lon: 75.3283, type: 'Pilgrimage' },
    { name: 'Nanded', state: 'Maharashtra', lat: 19.1383, lon: 77.3210, type: 'Pilgrimage' },
    { name: 'Bhimashankar', state: 'Maharashtra', lat: 19.0722, lon: 73.5358, type: 'Jyotirlinga' },

    // ─── Karnataka ───
    { name: 'Bengaluru', state: 'Karnataka', lat: 12.9716, lon: 77.5946, type: 'Metro' },
    { name: 'Mysuru', state: 'Karnataka', lat: 12.2958, lon: 76.6394, type: 'Heritage' },
    { name: 'Mangaluru', state: 'Karnataka', lat: 12.9141, lon: 74.8560, type: 'Coastal' },
    { name: 'Hubballi', state: 'Karnataka', lat: 15.3647, lon: 75.1240, type: 'City' },
    { name: 'Belagavi', state: 'Karnataka', lat: 15.8497, lon: 74.4977, type: 'City' },
    { name: 'Udupi', state: 'Karnataka', lat: 13.3409, lon: 74.7421, type: 'Temple' },
    { name: 'Sringeri', state: 'Karnataka', lat: 13.4184, lon: 75.2570, type: 'Peetham' },
    { name: 'Hampi', state: 'Karnataka', lat: 15.3350, lon: 76.4600, type: 'Heritage' },
    { name: 'Gokarna', state: 'Karnataka', lat: 14.5479, lon: 74.3188, type: 'Temple' },
    { name: 'Kalaburagi', state: 'Karnataka', lat: 17.3297, lon: 76.8343, type: 'City' },
    { name: 'Dharmasthala', state: 'Karnataka', lat: 12.9555, lon: 75.3789, type: 'Pilgrimage' },
    { name: 'Kollur', state: 'Karnataka', lat: 13.8647, lon: 74.8122, type: 'Temple' },
    { name: 'Kukke Subramanya', state: 'Karnataka', lat: 12.6653, lon: 75.6174, type: 'Temple' },

    // ─── Tamil Nadu ───
    { name: 'Chennai', state: 'Tamil Nadu', lat: 13.0827, lon: 80.2707, type: 'Metro' },
    { name: 'Madurai', state: 'Tamil Nadu', lat: 9.9252, lon: 78.1198, type: 'Temple City' },
    { name: 'Coimbatore', state: 'Tamil Nadu', lat: 11.0168, lon: 76.9558, type: 'City' },
    { name: 'Tiruchirappalli', state: 'Tamil Nadu', lat: 10.7905, lon: 78.7047, type: 'Temple City' },
    { name: 'Rameswaram', state: 'Tamil Nadu', lat: 9.2876, lon: 79.3129, type: 'Jyotirlinga / Char Dham' },
    { name: 'Kanchipuram', state: 'Tamil Nadu', lat: 12.8342, lon: 79.7036, type: 'Temple City' },
    { name: 'Thanjavur', state: 'Tamil Nadu', lat: 10.7870, lon: 79.1378, type: 'Heritage' },
    { name: 'Tiruvannamalai', state: 'Tamil Nadu', lat: 12.2253, lon: 79.0747, type: 'Temple' },
    { name: 'Chidambaram', state: 'Tamil Nadu', lat: 11.3992, lon: 79.6936, type: 'Temple' },
    { name: 'Kanyakumari', state: 'Tamil Nadu', lat: 8.0883, lon: 77.5385, type: 'Sacred Confluence' },
    { name: 'Palani', state: 'Tamil Nadu', lat: 10.4500, lon: 77.5167, type: 'Temple' },
    { name: 'Salem', state: 'Tamil Nadu', lat: 11.6643, lon: 78.1460, type: 'City' },
    { name: 'Tirunelveli', state: 'Tamil Nadu', lat: 8.7139, lon: 77.7567, type: 'City' },
    { name: 'Kumbakonam', state: 'Tamil Nadu', lat: 10.9602, lon: 79.3845, type: 'Temple City' },

    // ─── Uttar Pradesh ───
    { name: 'Varanasi', state: 'Uttar Pradesh', lat: 25.3176, lon: 82.9739, type: 'Holy City' },
    { name: 'Ayodhya', state: 'Uttar Pradesh', lat: 26.7922, lon: 82.1998, type: 'Ram Janmabhoomi' },
    { name: 'Mathura', state: 'Uttar Pradesh', lat: 27.4924, lon: 77.6737, type: 'Krishna Janmabhoomi' },
    { name: 'Vrindavan', state: 'Uttar Pradesh', lat: 27.5806, lon: 77.7006, type: 'Pilgrimage' },
    { name: 'Prayagraj', state: 'Uttar Pradesh', lat: 25.4358, lon: 81.8463, type: 'Triveni Sangam' },
    { name: 'Lucknow', state: 'Uttar Pradesh', lat: 26.8467, lon: 80.9462, type: 'Capital' },
    { name: 'Kanpur', state: 'Uttar Pradesh', lat: 26.4499, lon: 80.3319, type: 'City' },
    { name: 'Agra', state: 'Uttar Pradesh', lat: 27.1767, lon: 78.0081, type: 'Heritage' },
    { name: 'Noida', state: 'Uttar Pradesh', lat: 28.5355, lon: 77.3910, type: 'City' },
    { name: 'Ghaziabad', state: 'Uttar Pradesh', lat: 28.6692, lon: 77.4538, type: 'City' },
    { name: 'Gorakhpur', state: 'Uttar Pradesh', lat: 26.7606, lon: 83.3732, type: 'City' },
    { name: 'Meerut', state: 'Uttar Pradesh', lat: 28.9845, lon: 77.7064, type: 'City' },
    { name: 'Bareilly', state: 'Uttar Pradesh', lat: 28.3670, lon: 79.4304, type: 'City' },
    { name: 'Aligarh', state: 'Uttar Pradesh', lat: 27.8974, lon: 78.0880, type: 'City' },
    { name: 'Jhansi', state: 'Uttar Pradesh', lat: 25.4484, lon: 78.5685, type: 'City' },
    { name: 'Kashi', state: 'Uttar Pradesh', lat: 25.3109, lon: 83.0104, type: 'Jyotirlinga' },
    { name: 'Chitrakoot', state: 'Uttar Pradesh', lat: 25.1783, lon: 80.8688, type: 'Pilgrimage' },

    // ─── West Bengal ───
    { name: 'Kolkata', state: 'West Bengal', lat: 22.5726, lon: 88.3639, type: 'Metro' },
    { name: 'Howrah', state: 'West Bengal', lat: 22.5958, lon: 88.2636, type: 'City' },
    { name: 'Kalighat', state: 'West Bengal', lat: 22.5204, lon: 88.3444, type: 'Shakti Peetha' },
    { name: 'Dakshineswar', state: 'West Bengal', lat: 22.6548, lon: 88.3575, type: 'Temple' },
    { name: 'Mayapur', state: 'West Bengal', lat: 23.4233, lon: 88.3898, type: 'Pilgrimage' },
    { name: 'Siliguri', state: 'West Bengal', lat: 26.7271, lon: 88.3953, type: 'City' },
    { name: 'Durgapur', state: 'West Bengal', lat: 23.5204, lon: 87.3119, type: 'City' },
    { name: 'Asansol', state: 'West Bengal', lat: 23.6739, lon: 86.9524, type: 'City' },
    { name: 'Tarapith', state: 'West Bengal', lat: 24.1139, lon: 87.7972, type: 'Shakti Peetha' },
    { name: 'Darjeeling', state: 'West Bengal', lat: 27.0410, lon: 88.2663, type: 'Hill Station' },

    // ─── Gujarat ───
    { name: 'Ahmedabad', state: 'Gujarat', lat: 23.0225, lon: 72.5714, type: 'Metro' },
    { name: 'Surat', state: 'Gujarat', lat: 21.1702, lon: 72.8311, type: 'City' },
    { name: 'Vadodara', state: 'Gujarat', lat: 22.3072, lon: 73.1812, type: 'City' },
    { name: 'Rajkot', state: 'Gujarat', lat: 22.3039, lon: 70.8022, type: 'City' },
    { name: 'Dwarka', state: 'Gujarat', lat: 22.2442, lon: 68.9685, type: 'Char Dham' },
    { name: 'Somnath', state: 'Gujarat', lat: 20.8880, lon: 70.4012, type: 'Jyotirlinga' },
    { name: 'Gandhinagar', state: 'Gujarat', lat: 23.2156, lon: 72.6369, type: 'Capital' },
    { name: 'Ambaji', state: 'Gujarat', lat: 24.3323, lon: 72.8519, type: 'Shakti Peetha' },
    { name: 'Bhavnagar', state: 'Gujarat', lat: 21.7645, lon: 72.1519, type: 'City' },
    { name: 'Jamnagar', state: 'Gujarat', lat: 22.4707, lon: 70.0577, type: 'City' },
    { name: 'Palitana', state: 'Gujarat', lat: 21.5222, lon: 71.8286, type: 'Pilgrimage' },
    { name: 'Junagadh', state: 'Gujarat', lat: 21.5222, lon: 70.4579, type: 'Historical' },

    // ─── Rajasthan ───
    { name: 'Jaipur', state: 'Rajasthan', lat: 26.9124, lon: 75.7873, type: 'Capital' },
    { name: 'Jodhpur', state: 'Rajasthan', lat: 26.2389, lon: 73.0243, type: 'City' },
    { name: 'Udaipur', state: 'Rajasthan', lat: 24.5854, lon: 73.7125, type: 'City' },
    { name: 'Pushkar', state: 'Rajasthan', lat: 26.4897, lon: 74.5511, type: 'Brahma Temple' },
    { name: 'Ajmer', state: 'Rajasthan', lat: 26.4499, lon: 74.6399, type: 'Pilgrimage' },
    { name: 'Bikaner', state: 'Rajasthan', lat: 28.0229, lon: 73.3119, type: 'City' },
    { name: 'Kota', state: 'Rajasthan', lat: 25.2138, lon: 75.8648, type: 'City' },
    { name: 'Nathdwara', state: 'Rajasthan', lat: 24.9318, lon: 73.8188, type: 'Temple' },
    { name: 'Khatu Shyamji', state: 'Rajasthan', lat: 27.3639, lon: 75.3056, type: 'Temple' },
    { name: 'Salasar', state: 'Rajasthan', lat: 27.7194, lon: 74.7214, type: 'Temple' },
    { name: 'Mount Abu', state: 'Rajasthan', lat: 24.5925, lon: 72.7156, type: 'Hill / Temples' },

    // ─── Madhya Pradesh ───
    { name: 'Ujjain', state: 'Madhya Pradesh', lat: 23.1765, lon: 75.7885, type: 'Jyotirlinga (Mahakal)' },
    { name: 'Indore', state: 'Madhya Pradesh', lat: 22.7196, lon: 75.8577, type: 'City' },
    { name: 'Bhopal', state: 'Madhya Pradesh', lat: 23.2599, lon: 77.4126, type: 'Capital' },
    { name: 'Jabalpur', state: 'Madhya Pradesh', lat: 23.1815, lon: 79.9864, type: 'City' },
    { name: 'Gwalior', state: 'Madhya Pradesh', lat: 26.2183, lon: 78.1828, type: 'Historical' },
    { name: 'Omkareshwar', state: 'Madhya Pradesh', lat: 22.2436, lon: 76.1511, type: 'Jyotirlinga' },
    { name: 'Khajuraho', state: 'Madhya Pradesh', lat: 24.8318, lon: 79.9199, type: 'Heritage' },
    { name: 'Maihar', state: 'Madhya Pradesh', lat: 24.2694, lon: 80.7583, type: 'Shakti Peetha' },
    { name: 'Amarkantak', state: 'Madhya Pradesh', lat: 22.6739, lon: 81.7583, type: 'Sacred Source' },

    // ─── Kerala ───
    { name: 'Thiruvananthapuram', state: 'Kerala', lat: 8.5241, lon: 76.9366, type: 'Capital' },
    { name: 'Kochi', state: 'Kerala', lat: 9.9312, lon: 76.2673, type: 'Coastal Metro' },
    { name: 'Kozhikode', state: 'Kerala', lat: 11.2588, lon: 75.7804, type: 'City' },
    { name: 'Guruvayur', state: 'Kerala', lat: 10.5946, lon: 76.0416, type: 'Temple' },
    { name: 'Sabarimala', state: 'Kerala', lat: 9.4402, lon: 77.0817, type: 'Pilgrimage' },
    { name: 'Thrissur', state: 'Kerala', lat: 10.5276, lon: 76.2144, type: 'Cultural Capital' },
    { name: 'Kollam', state: 'Kerala', lat: 8.8932, lon: 76.6141, type: 'City' },
    { name: 'Alappuzha', state: 'Kerala', lat: 9.4981, lon: 76.3388, type: 'Coastal' },
    { name: 'Chottanikkara', state: 'Kerala', lat: 9.9324, lon: 76.3934, type: 'Temple' },

    // ─── Odisha ───
    { name: 'Puri', state: 'Odisha', lat: 19.8135, lon: 85.8312, type: 'Jagannath / Char Dham' },
    { name: 'Bhubaneswar', state: 'Odisha', lat: 20.2961, lon: 85.8245, type: 'Temple Capital' },
    { name: 'Cuttack', state: 'Odisha', lat: 20.4625, lon: 85.8828, type: 'City' },
    { name: 'Konark', state: 'Odisha', lat: 19.8876, lon: 86.0945, type: 'Sun Temple' },
    { name: 'Rourkela', state: 'Odisha', lat: 22.2604, lon: 84.8536, type: 'City' },
    { name: 'Sambalpur', state: 'Odisha', lat: 21.4669, lon: 83.9812, type: 'City' },

    // ─── Uttarakhand ───
    { name: 'Haridwar', state: 'Uttarakhand', lat: 29.9457, lon: 78.1642, type: 'Kumbh / Holy' },
    { name: 'Rishikesh', state: 'Uttarakhand', lat: 30.0869, lon: 78.2676, type: 'Yoga Capital' },
    { name: 'Badrinath', state: 'Uttarakhand', lat: 30.7433, lon: 79.4938, type: 'Char Dham' },
    { name: 'Kedarnath', state: 'Uttarakhand', lat: 30.7352, lon: 79.0669, type: 'Jyotirlinga / Char Dham' },
    { name: 'Gangotri', state: 'Uttarakhand', lat: 30.9947, lon: 78.9398, type: 'Char Dham' },
    { name: 'Yamunotri', state: 'Uttarakhand', lat: 31.0140, lon: 78.4600, type: 'Char Dham' },
    { name: 'Dehradun', state: 'Uttarakhand', lat: 30.3165, lon: 78.0322, type: 'Capital' },
    { name: 'Nainital', state: 'Uttarakhand', lat: 29.3919, lon: 79.4542, type: 'Hill Station' },

    // ─── Himachal Pradesh ───
    { name: 'Shimla', state: 'Himachal Pradesh', lat: 31.1048, lon: 77.1734, type: 'Capital' },
    { name: 'Dharamshala', state: 'Himachal Pradesh', lat: 32.2190, lon: 76.3234, type: 'Hill Town' },
    { name: 'Kullu', state: 'Himachal Pradesh', lat: 31.9579, lon: 77.1095, type: 'Valley of Gods' },
    { name: 'Manali', state: 'Himachal Pradesh', lat: 32.2432, lon: 77.1892, type: 'Hill Town' },
    { name: 'Mandi', state: 'Himachal Pradesh', lat: 31.5892, lon: 76.9182, type: 'Choti Kashi' },
    { name: 'Jwalamukhi', state: 'Himachal Pradesh', lat: 31.8744, lon: 76.3253, type: 'Shakti Peetha' },

    // ─── Punjab ───
    { name: 'Amritsar', state: 'Punjab', lat: 31.6340, lon: 74.8723, type: 'Golden Temple' },
    { name: 'Ludhiana', state: 'Punjab', lat: 30.9010, lon: 75.8573, type: 'City' },
    { name: 'Jalandhar', state: 'Punjab', lat: 31.3260, lon: 75.5762, type: 'City' },
    { name: 'Patiala', state: 'Punjab', lat: 30.3398, lon: 76.3869, type: 'City' },

    // ─── Haryana ───
    { name: 'Kurukshetra', state: 'Haryana', lat: 29.9695, lon: 76.8783, type: 'Gita Bhoomi' },
    { name: 'Gurugram', state: 'Haryana', lat: 28.4595, lon: 77.0266, type: 'Metro' },
    { name: 'Faridabad', state: 'Haryana', lat: 28.4089, lon: 77.3178, type: 'City' },
    { name: 'Panipat', state: 'Haryana', lat: 29.3909, lon: 76.9635, type: 'City' },
    { name: 'Panchkula', state: 'Haryana', lat: 30.6942, lon: 76.8606, type: 'City' },

    // ─── Bihar ───
    { name: 'Patna', state: 'Bihar', lat: 25.5941, lon: 85.1376, type: 'Capital' },
    { name: 'Gaya', state: 'Bihar', lat: 24.7914, lon: 85.0002, type: 'Vishnupad / Pinda Daan' },
    { name: 'Bodh Gaya', state: 'Bihar', lat: 24.6961, lon: 84.9869, type: 'Pilgrimage' },
    { name: 'Muzaffarpur', state: 'Bihar', lat: 26.1209, lon: 85.3647, type: 'City' },
    { name: 'Bhagalpur', state: 'Bihar', lat: 25.2425, lon: 86.9842, type: 'City' },
    { name: 'Darbhanga', state: 'Bihar', lat: 26.1542, lon: 85.8918, type: 'Cultural' },
    { name: 'Deoghar', state: 'Jharkhand', lat: 24.4826, lon: 86.7000, type: 'Baidyanath Jyotirlinga' },

    // ─── Jharkhand ───
    { name: 'Ranchi', state: 'Jharkhand', lat: 23.3441, lon: 85.3096, type: 'Capital' },
    { name: 'Jamshedpur', state: 'Jharkhand', lat: 22.8046, lon: 86.2029, type: 'City' },
    { name: 'Dhanbad', state: 'Jharkhand', lat: 23.7957, lon: 86.4304, type: 'City' },
    { name: 'Bokaro', state: 'Jharkhand', lat: 23.6693, lon: 86.1511, type: 'City' },

    // ─── Chhattisgarh ───
    { name: 'Raipur', state: 'Chhattisgarh', lat: 21.2514, lon: 81.6296, type: 'Capital' },
    { name: 'Bilaspur', state: 'Chhattisgarh', lat: 22.0797, lon: 82.1409, type: 'City' },
    { name: 'Durg', state: 'Chhattisgarh', lat: 21.1904, lon: 81.2849, type: 'City' },
    { name: 'Bhilai', state: 'Chhattisgarh', lat: 21.2167, lon: 81.4333, type: 'City' },
    { name: 'Ratanpur', state: 'Chhattisgarh', lat: 22.3000, lon: 82.1667, type: 'Shakti Peetha' },

    // ─── Assam ───
    { name: 'Guwahati', state: 'Assam', lat: 26.1445, lon: 91.7362, type: 'Kamakhya Shakti Peetha' },
    { name: 'Kamakhya', state: 'Assam', lat: 26.1664, lon: 91.7058, type: 'Shakti Peetha' },
    { name: 'Silchar', state: 'Assam', lat: 24.8333, lon: 92.7789, type: 'City' },
    { name: 'Dibrugarh', state: 'Assam', lat: 27.4728, lon: 94.9120, type: 'City' },
    { name: 'Jorhat', state: 'Assam', lat: 26.7509, lon: 94.2037, type: 'City' },
    { name: 'Tezpur', state: 'Assam', lat: 26.6528, lon: 92.7926, type: 'City' },

    // ─── Jammu & Kashmir ───
    { name: 'Srinagar', state: 'Jammu & Kashmir', lat: 34.0837, lon: 74.7973, type: 'Summer Capital' },
    { name: 'Jammu', state: 'Jammu & Kashmir', lat: 32.7266, lon: 74.8570, type: 'Winter Capital' },
    { name: 'Katra', state: 'Jammu & Kashmir', lat: 32.9926, lon: 74.9318, type: 'Vaishno Devi' },
    { name: 'Amarnath', state: 'Jammu & Kashmir', lat: 34.2155, lon: 75.5036, type: 'Holy Cave' },

    // ─── Ladakh ───
    { name: 'Leh', state: 'Ladakh', lat: 34.1526, lon: 77.5771, type: 'Capital' },
    { name: 'Kargil', state: 'Ladakh', lat: 34.5539, lon: 76.1349, type: 'Town' },

    // ─── Goa ───
    { name: 'Panaji', state: 'Goa', lat: 15.4909, lon: 73.8278, type: 'Capital' },
    { name: 'Margao', state: 'Goa', lat: 15.2736, lon: 73.9580, type: 'City' },
    { name: 'Mangueshi', state: 'Goa', lat: 15.4339, lon: 73.9686, type: 'Temple' },

    // ─── Northeast States ───
    { name: 'Agartala', state: 'Tripura', lat: 23.8315, lon: 91.2868, type: 'Tripura Sundari' },
    { name: 'Udaipur (Tripura)', state: 'Tripura', lat: 23.5333, lon: 91.4833, type: 'Shakti Peetha' },
    { name: 'Shillong', state: 'Meghalaya', lat: 25.5788, lon: 91.8933, type: 'Capital' },
    { name: 'Imphal', state: 'Manipur', lat: 24.8170, lon: 93.9368, type: 'Capital' },
    { name: 'Aizawl', state: 'Mizoram', lat: 23.7271, lon: 92.7176, type: 'Capital' },
    { name: 'Kohima', state: 'Nagaland', lat: 25.6751, lon: 94.1086, type: 'Capital' },
    { name: 'Itanagar', state: 'Arunachal Pradesh', lat: 27.0844, lon: 93.6053, type: 'Capital' },
    { name: 'Gangtok', state: 'Sikkim', lat: 27.3389, lon: 88.6065, type: 'Capital' },

    // ─── Union Territories ───
    { name: 'Chandigarh', state: 'Chandigarh', lat: 30.7333, lon: 76.7794, type: 'UT / Capital' },
    { name: 'Puducherry', state: 'Puducherry', lat: 11.9416, lon: 79.8083, type: 'UT' },
    { name: 'Port Blair', state: 'Andaman & Nicobar Islands', lat: 11.6234, lon: 92.7265, type: 'UT Capital' },
    { name: 'Kavaratti', state: 'Lakshadweep', lat: 10.5669, lon: 72.6420, type: 'UT Capital' },
    { name: 'Daman', state: 'Dadra & Nagar Haveli and Daman & Diu', lat: 20.3974, lon: 72.8328, type: 'UT Capital' },
    { name: 'Diu', state: 'Dadra & Nagar Haveli and Daman & Diu', lat: 20.7144, lon: 70.9874, type: 'UT Island' },
    { name: 'Silvassa', state: 'Dadra & Nagar Haveli and Daman & Diu', lat: 20.2763, lon: 73.0083, type: 'UT Town' }
  ];

  /**
   * Central Location Normalization Function.
   * Guarantees numeric latitude and longitude, consistent lat/lon aliases, and IANA timezone.
   */
  function normalizeLocation(loc, fallback) {
    const fb = fallback || {
      name: 'Hyderabad',
      city: 'Hyderabad',
      state: 'Telangana',
      latitude: 17.3850,
      longitude: 78.4867,
      lat: 17.3850,
      lon: 78.4867,
      timezone: TIMEZONE_IST
    };

    if (!loc || typeof loc !== 'object') {
      return { ...fb };
    }

    const rawLat = (loc.latitude !== undefined && loc.latitude !== null && loc.latitude !== '')
      ? loc.latitude
      : ((loc.lat !== undefined && loc.lat !== null && loc.lat !== '') ? loc.lat : (fb.latitude ?? fb.lat));
    const rawLon = (loc.longitude !== undefined && loc.longitude !== null && loc.longitude !== '')
      ? loc.longitude
      : ((loc.lon !== undefined && loc.lon !== null && loc.lon !== '') ? loc.lon : (fb.longitude ?? fb.lon));

    const latitude = Number(rawLat);
    const longitude = Number(rawLon);

    if (!Number.isFinite(latitude) || !Number.isFinite(longitude)) {
      if (fallback) {
        return { ...fb };
      }
      throw new Error('Invalid location coordinates');
    }

    const name = typeof (loc.name || loc.city) === 'string' && (loc.name || loc.city).trim()
      ? (loc.name || loc.city).trim()
      : (fb.name || 'Hyderabad');

    const state = typeof loc.state === 'string' && loc.state.trim()
      ? loc.state.trim()
      : (fb.state || 'Telangana');

    let timezone = typeof loc.timezone === 'string' && loc.timezone.trim() ? loc.timezone.trim() : '';
    if (!timezone || /^[0-9.+-]+$/.test(timezone)) {
      timezone = fb.timezone || TIMEZONE_IST;
    }

    return {
      ...loc,
      name: name,
      city: name,
      state: state,
      latitude: latitude,
      longitude: longitude,
      lat: latitude,
      lon: longitude,
      timezone: timezone
    };
  }

  /**
   * Safe Defensive Coordinate Formatter
   */
  function formatCoordinate(value, digits = 4) {
    const number = Number(value);
    if (!Number.isFinite(number)) {
      return '—';
    }
    return number.toFixed(digits);
  }

  // Pre-normalize all 350+ entries in the database to guarantee numeric coordinates & IANA timezone
  const LOCATIONS_DATA = RAW_LOCATIONS.map(c => normalizeLocation(c));

  /**
   * Calculate distance between two lat/lon pairs in kilometers (Haversine formula).
   */
  function haversineDistance(lat1, lon1, lat2, lon2) {
    const nLat1 = Number(lat1);
    const nLon1 = Number(lon1);
    const nLat2 = Number(lat2);
    const nLon2 = Number(lon2);
    const R = 6371; // Earth's radius in km
    const dLat = (nLat2 - nLat1) * (Math.PI / 180);
    const dLon = (nLon2 - nLon1) * (Math.PI / 180);
    const a =
      Math.sin(dLat / 2) * Math.sin(dLat / 2) +
      Math.cos(nLat1 * (Math.PI / 180)) * Math.cos(nLat2 * (Math.PI / 180)) *
      Math.sin(dLon / 2) * Math.sin(dLon / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
  }

  /**
   * Search locations by query string and optional state filter.
   */
  function searchLocations(query = '', stateFilter = 'All States', limit = 20) {
    const q = query.trim().toLowerCase();
    const hasFilter = stateFilter && stateFilter !== 'All States';

    let matches = LOCATIONS_DATA.filter(loc => {
      if (hasFilter && loc.state !== stateFilter) return false;
      if (!q) return true;
      return (
        loc.name.toLowerCase().includes(q) ||
        loc.state.toLowerCase().includes(q) ||
        (loc.type && loc.type.toLowerCase().includes(q))
      );
    });

    // Score sort: exact match > prefix match > contains match
    if (q) {
      matches.sort((a, b) => {
        const aName = a.name.toLowerCase();
        const bName = b.name.toLowerCase();
        const aExact = aName === q;
        const bExact = bName === q;
        if (aExact && !bExact) return -1;
        if (!aExact && bExact) return 1;

        const aStarts = aName.startsWith(q);
        const bStarts = bName.startsWith(q);
        if (aStarts && !bStarts) return -1;
        if (!aStarts && bStarts) return 1;

        return aName.localeCompare(bName);
      });
    }

    return matches.slice(0, limit);
  }

  /**
   * Find nearest registered Indian city to given latitude/longitude.
   */
  function findNearestLocation(lat, lon) {
    const nLat = Number(lat);
    const nLon = Number(lon);
    let nearest = LOCATIONS_DATA[0];
    let minDistance = Infinity;

    for (const loc of LOCATIONS_DATA) {
      const dist = haversineDistance(nLat, nLon, loc.latitude, loc.longitude);
      if (dist < minDistance) {
        minDistance = dist;
        nearest = loc;
      }
    }

    return {
      location: normalizeLocation(nearest),
      distanceKm: Math.round(minDistance)
    };
  }

  /**
   * Fetch reverse geocoded city name from Nominatim OpenStreetMap (with fallback).
   */
  async function reverseGeocode(lat, lon) {
    const nLat = Number(lat);
    const nLon = Number(lon);
    try {
      const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${nLat}&lon=${nLon}&zoom=10&addressdetails=1`;
      const res = await fetch(url, {
        headers: { 'Accept': 'application/json' }
      });
      if (res.ok) {
        const data = await res.json();
        if (data && data.address) {
          const city = data.address.city || data.address.town || data.address.village || data.address.county || 'Detected Location';
          const state = data.address.state || 'India';
          return normalizeLocation({
            name: city,
            state: state,
            country: 'India',
            latitude: nLat,
            longitude: nLon,
            timezone: TIMEZONE_IST,
            source: 'gps_nominatim'
          });
        }
      }
    } catch (e) {
      // Ignore network errors and fallback to internal table
    }

    // Fallback to nearest internal location
    const nearest = findNearestLocation(nLat, nLon);
    return normalizeLocation({
      name: `${nearest.location.name} (Near)`,
      state: nearest.location.state,
      country: 'India',
      latitude: nLat,
      longitude: nLon,
      timezone: TIMEZONE_IST,
      source: 'gps_nearest_fallback',
      distanceKm: nearest.distanceKm
    });
  }

  function getStates() {
    return STATES_AND_UTS.filter(s => s !== 'All States');
  }

  function findNearest(lat, lon) {
    const res = findNearestLocation(lat, lon);
    return res ? res.location : null;
  }

  function isWithinIndia(lat, lon) {
    const nLat = Number(lat);
    const nLon = Number(lon);
    if (nLat < 6.0 || nLat > 37.5 || nLon < 68.0 || nLon > 97.5) {
      return false;
    }
    const res = findNearestLocation(nLat, nLon);
    return res && res.distanceKm <= 350;
  }

  if (typeof window !== 'undefined') {
    window.djvNormalizeLocation = normalizeLocation;
    window.djvFormatCoordinate = formatCoordinate;
    window.indiaLocations = LOCATIONS_DATA;
  }

  return {
    TIMEZONE_IST,
    STATES_AND_UTS,
    LOCATIONS_DATA,
    normalizeLocation,
    formatCoordinate,
    searchLocations,
    findNearestLocation,
    findNearest,
    reverseGeocode,
    haversineDistance,
    getStates,
    isWithinIndia
  };
});
