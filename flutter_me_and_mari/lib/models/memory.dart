class Memory {
  final int id;
  final String title;
  final String photo;
  final String happyMeter;
  final String description;
  final String location;
  final String createdAt;
  final String detectedPerson;

  Memory({
    required this.id,
    required this.title,
    required this.photo,
    required this.happyMeter,
    required this.description,
    required this.location,
    required this.createdAt,
    required this.detectedPerson,
  });

  factory Memory.fromJson(Map<String, dynamic> json) {
    return Memory(
      id: int.parse(json['id'].toString()),
      title: json['title'] ?? '',
      photo: json['photo'] ?? '',
      happyMeter: json['happy_meter'] ?? '',
      description: json['description'] ?? '',
      location: json['location'] ?? '',
      createdAt: json['created_at'] ?? '',
      detectedPerson: json['detected_person'] ?? 'unknown',
    );
  }
}
