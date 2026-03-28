import 'dart:convert';
import 'package:http/http.dart' as http;
import '../models/memory.dart';

class ApiService {
  // Ganti dengan IP Local Laptop Anda (misal: 192.168.1.5) jika tes di HP Android asli
  static const String baseUrl = 'http://192.168.5.138/MeAndMari/api'; 
  static const String uploadUrl = 'http://192.168.5.138/MeAndMari/uploads/';

  Future<List<Memory>> getMemories({String search = ''}) async {
    try {
      final response = await http
          .get(Uri.parse('$baseUrl/get_memories.php?search=$search'))
          .timeout(const Duration(seconds: 30)); // Increased
      
      if (response.statusCode == 200) {
        final body = jsonDecode(response.body);
        if (body['status'] == 'success') {
          List<dynamic> data = body['data'];
          return data.map((item) => Memory.fromJson(item)).toList();
        }
      }
      throw Exception('Server Error: ${response.statusCode}');
    } catch (e) {
      print('API Error (getMemories): $e');
      throw Exception('Gagal memuat kenangan: $e');
    }
  }

  Future<Map<String, dynamic>> getMemoryDetail(int id) async {
    try {
      final response = await http
          .get(Uri.parse('$baseUrl/get_memory_detail.php?id=$id'))
          .timeout(const Duration(seconds: 30));
      if (response.statusCode == 200) {
        return jsonDecode(response.body);
      }
      throw Exception('Server Error: ${response.statusCode}');
    } catch (e) {
      throw Exception('Gagal memuat detail: $e');
    }
  }

  Future<Map<String, dynamic>> login(String username, String password) async {
    try {
      final response = await http
          .post(
            Uri.parse('$baseUrl/login.php'),
            body: jsonEncode({'username': username, 'password': password}),
          )
          .timeout(const Duration(seconds: 30));
      return jsonDecode(response.body);
    } catch (e) {
      return {'status': 'error', 'message': 'Kesalahan koneksi: $e'};
    }
  }

  Future<Map<String, dynamic>> getUserProfile(String username) async {
    try {
      final response = await http
          .get(Uri.parse('$baseUrl/get_user_profile.php?username=$username'))
          .timeout(const Duration(seconds: 30));
      if (response.statusCode == 200) {
        return jsonDecode(response.body);
      }
      throw Exception('Server Error');
    } catch (e) {
      throw Exception('Gagal memuat profil: $e');
    }
  }

  Future<Map<String, dynamic>> deleteMemory(int id) async {
    try {
      final response = await http
          .post(
            Uri.parse('$baseUrl/delete_memory.php'),
            body: jsonEncode({'id': id}),
          )
          .timeout(const Duration(seconds: 10));
      return jsonDecode(response.body);
    } catch (e) {
      return {'status': 'error', 'message': 'Gagal menghapus: $e'};
    }
  }

  Future<Map<String, dynamic>> saveMemory(Map<String, dynamic> data) async {
    try {
      final response = await http
          .post(
            Uri.parse('$baseUrl/save_memory.php'),
            body: jsonEncode(data),
          )
          .timeout(const Duration(seconds: 20));
      return jsonDecode(response.body);
    } catch (e) {
      return {'status': 'error', 'message': 'Gagal menyimpan: $e'};
    }
  }

  Future<Map<String, dynamic>> getMeetUsData() async {
    try {
      final response = await http
          .get(Uri.parse('$baseUrl/get_meet_us.php'))
          .timeout(const Duration(seconds: 15));
      if (response.statusCode == 200) {
        return jsonDecode(response.body);
      }
      throw Exception('Server Error: ${response.statusCode}');
    } catch (e) {
      print('API Error (getMeetUsData): $e');
      if (e.toString().contains('TimeoutException')) {
        throw Exception('Koneksi timeout (Meet Us). Periksa koneksi internet.');
      }
      throw Exception('Gagal memuat Meet Us: $e');
    }
  }

  Future<String?> uploadFile(String path) async {
    var request = http.MultipartRequest('POST', Uri.parse('$baseUrl/upload_file.php'));
    request.files.add(await http.MultipartFile.fromPath('file', path));
    var res = await request.send();
    var responseData = await res.stream.bytesToString();
    var decoded = jsonDecode(responseData);
    if (decoded['status'] == 'success') {
      return decoded['data']['filename'];
    }
    return null;
  }

  Future<List<String>> uploadMultipleFiles(List<String> paths) async {
    List<String> uploadedNames = [];
    for (String path in paths) {
      final name = await uploadFile(path);
      if (name != null) uploadedNames.add(name);
    }
    return uploadedNames;
  }
}
